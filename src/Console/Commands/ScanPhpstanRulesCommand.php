<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\PhpstanRules\InstalledRulePackageFilter;
use App\PhpstanRules\PhpstanRuleScanner;
use App\Repository\PhpstanRulePackageRepository;
use App\ValueObject\PhpstanRule;
use Illuminate\Console\Command;
use RuntimeException;

final class ScanPhpstanRulesCommand extends Command
{
    private const string OUTPUT_FILENAME = 'discover-phpstan-rules.json';

    protected $signature = 'app:scan-phpstan-rules';

    protected $description = 'Scan installed PHPStan rule packages and write resources/discover-phpstan-rules.json';

    public function __construct(
        private readonly PhpstanRulePackageRepository $phpstanRulePackageRepository,
        private readonly InstalledRulePackageFilter $installedRulePackageFilter,
        private readonly PhpstanRuleScanner $phpstanRuleScanner,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $packages = $this->phpstanRulePackageRepository->fetchAllByPackage();
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $installedPackages = $this->installedRulePackageFilter->filterInstalled($packages);

        if ($installedPackages === []) {
            $this->warn('No PHPStan rule packages installed - leaving existing JSON intact.');
            return self::SUCCESS;
        }

        $rules = $this->phpstanRuleScanner->scan($installedPackages);

        $outputPath = resource_path(self::OUTPUT_FILENAME);
        file_put_contents($outputPath, json_encode([
            'generated_at' => date('c'),
            'count' => count($rules),
            'rules' => array_map($this->toJsonShape(...), $rules),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

        $withSnippets = count(array_filter(
            $rules,
            static fn (PhpstanRule $rule): bool => $rule->getWrongCode() !== '' || $rule->getCorrectCode() !== '',
        ));

        $this->info(sprintf(
            'Scanned %d rules across %d of %d packages (%d with code snippets) -> %s',
            count($rules),
            count($installedPackages),
            count($packages),
            $withSnippets,
            $outputPath,
        ));

        return self::SUCCESS;
    }

    /**
     * @return array<string, string>
     */
    private function toJsonShape(PhpstanRule $rule): array
    {
        return [
            'group' => $rule->getGroup(),
            'package' => $rule->getPackage(),
            'class' => $rule->getClass(),
            'name' => $rule->getName(),
            'message' => $rule->getMessage(),
            'description' => $rule->getDescription(),
            'node_type' => $rule->getNodeType(),
            'wrong_code' => $rule->getWrongCode(),
            'correct_code' => $rule->getCorrectCode(),
            'identifier' => $rule->getIdentifier(),
            'tip' => $rule->getTip(),
        ];
    }
}
