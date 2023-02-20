<?php

declare(strict_types=1);

namespace TomasVotruba\Utils\Rector\Rector\ClassMethod;

use Nette\Utils\FileSystem;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use Rector\Core\Application\FileSystem\RemovedAndAddedFilesCollector;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\PhpParser\Printer\BetterStandardPrinter;
use Rector\Core\Rector\AbstractRector;
use Rector\FileSystemRector\ValueObject\AddedFileWithContent;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use TomasVotruba\Utils\Rector\NodeFactory\RouteGetCallFactory;
use TomasVotruba\Utils\Rector\ValueObject\ValueObject\RouteMetadata;
use TomasVotruba\Website\Exception\ShouldNotHappenException;
use Webmozart\Assert\Assert;

/**
 * @see \TomasVotruba\Utils\Tests\Rector\Rector\ClassMethod\SymfonyRouteAttributesToLaravelRouteFileRectorTest
 */
final class SymfonyRouteAttributesToLaravelRouteFileRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const ROUTES_FILE_PATH = 'routes_file_path';

    private string $routesFilePath;

    public function __construct(
        private readonly BetterStandardPrinter $betterStandardPrinter,
        private readonly RemovedAndAddedFilesCollector $removedAndAddedFilesCollector,
        private readonly RouteGetCallFactory $routeGetCallFactory,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Move Symfony route from attributes to static Laravel routes in routes/web.php file',
            [
                // ...
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?ClassMethod
    {
        if ($node->attrGroups === []) {
            return null;
        }

        $hasChanged = false;

        foreach ($node->attrGroups as $key => $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                if (! $this->isName($attribute->name, 'Symfony\Component\Routing\Annotation\Route')) {
                    continue;
                }

                $routeMetadata = $this->resolveRouteMetadata($attribute, $node);
                if (! $routeMetadata instanceof RouteMetadata) {
                    continue;
                }

                $hasChanged = true;
                unset($node->attrGroups[$key]);

                $routeCall = $this->routeGetCallFactory->create($routeMetadata);

                $printedRouteGet = $this->betterStandardPrinter->print(new Expression($routeCall)) . PHP_EOL;

                // ensure directory exists
                $routesDirectory = dirname($this->routesFilePath);
                if (! file_exists($routesDirectory)) {
                    FileSystem::createDir($routesDirectory);
                }

                // open with a tag
                if (! file_exists($this->routesFilePath)) {
                    $printedRouteGet = '<?php' . PHP_EOL . PHP_EOL . $printedRouteGet;
                }

                // skip adding real file in tests
                if (! defined('PHPUNIT_COMPOSER_INSTALL')) {
                    file_put_contents($this->routesFilePath, $printedRouteGet . PHP_EOL, FILE_APPEND);
                }

                // for tests B-)
                $addedFileWithContent = new AddedFileWithContent($this->routesFilePath, $printedRouteGet);
                $this->removedAndAddedFilesCollector->addAddedFile($addedFileWithContent);
            }
        }

        if ($hasChanged) {
            return $node;
        }

        return null;
    }

    public function configure(array $configuration): void
    {
        Assert::keyExists($configuration, 'routes_file_path');
        $this->routesFilePath = $configuration['routes_file_path'];
    }

    private function resolveRouteMetadata(Attribute $attribute, ClassMethod $classMethod): RouteMetadata
    {
        $routePath = $this->resolveRoutePath($attribute);

        $routeName = null;
        $routeRequirements = [];

        foreach ($attribute->args as $arg) {
            if (! $arg->name instanceof Identifier) {
                continue;
            }

            if ($this->isName($arg->name, 'name')) {
                $routeName = $this->valueResolver->getValue($arg->value);
            } elseif ($this->isName($arg->name, 'requirements')) {
                $routeRequirements = $this->valueResolver->getValue($arg->value);
            }
        }

        $routeTarget = $this->resolveRouteTaret($classMethod);
        return new RouteMetadata($routePath, $routeTarget, $routeName, $routeRequirements);
    }

    private function resolveRouteTaret(ClassMethod $classMethod): string
    {
        if ($this->isName($classMethod, '__invoke')) {
            // class is the target :)
            $class = $classMethod->getAttribute(AttributeKey::PARENT_NODE);
            if (! $class instanceof Node\Stmt\Class_) {
                throw new ShouldNotHappenException();
            }

            $routeTarget = $this->getName($class);
        } else {
            // not handled yet
            throw new ShouldNotHappenException();
        }

        Assert::string($routeTarget);

        return $routeTarget;
    }

    private function resolveRoutePath(Attribute $attribute): string
    {
        foreach ($attribute->args as $arg) {
            if ($arg->name instanceof Identifier) {
                if ($this->isName($arg->name, 'path')) {
                    return $this->valueResolver->getValue($arg->value);
                }
            } else {
                // implicit path
                return $this->valueResolver->getValue($arg->value);
            }
        }

        throw new ShouldNotHappenException();
    }
}
