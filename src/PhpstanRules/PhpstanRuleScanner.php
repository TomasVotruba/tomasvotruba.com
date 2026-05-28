<?php

declare(strict_types=1);

namespace App\PhpstanRules;

use App\ValueObject\PhpstanRule;
use App\ValueObject\PhpstanRulePackage;

final readonly class PhpstanRuleScanner
{
    public function __construct(
        private RuleFileFinder $ruleFileFinder,
        private RuleClassParser $ruleClassParser,
        private ReadmeSectionParser $readmeSectionParser,
        private CodeSnippetExtractor $codeSnippetExtractor,
    ) {
    }

    /**
     * @param list<PhpstanRulePackage> $packages
     * @return list<PhpstanRule>
     */
    public function scan(array $packages): array
    {
        $vendorRoot = base_path('vendor');

        $rules = [];
        foreach ($packages as $package) {
            $packageDir = $vendorRoot . '/' . $package->getPackage();
            $sections = $this->readmeSectionParser->parseSections($packageDir);

            foreach ($this->ruleFileFinder->findInPackage($packageDir) as $file) {
                $parsed = $this->ruleClassParser->parse($file, $packageDir);
                if ($parsed === null) {
                    continue;
                }

                $snippets = $this->codeSnippetExtractor->extractFor($parsed->getShortName(), $sections);

                $rules[] = new PhpstanRule(
                    group: $package->getGroup(),
                    package: $package->getPackage(),
                    class: $parsed->getFullyQualifiedName(),
                    name: $parsed->getShortName(),
                    message: $parsed->getMessage(),
                    description: $snippets->getDescription() !== '' ? $snippets->getDescription() : $parsed->getDocComment(),
                    nodeType: $parsed->getNodeType(),
                    wrongCode: $snippets->getWrongCode(),
                    correctCode: $snippets->getCorrectCode(),
                    identifier: $parsed->getIdentifier(),
                    tip: $parsed->getTip(),
                );
            }
        }

        usort(
            $rules,
            static fn (PhpstanRule $a, PhpstanRule $b): int =>
                $a->getGroup() <=> $b->getGroup() ?: $a->getName() <=> $b->getName()
        );

        return $rules;
    }
}
