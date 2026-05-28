<?php

declare(strict_types=1);

namespace App\PHPStanRules;

use App\PHPStanRules\ValueObject\ParsedRuleClass;
use App\PHPStanRules\ValueObject\PHPStanRule;
use App\PHPStanRules\ValueObject\PHPStanRulePackage;

final readonly class PHPStanRuleScanner
{
    public function __construct(
        private RuleFileFinder $ruleFileFinder,
        private RuleClassParser $ruleClassParser,
        private ReadmeSectionParser $readmeSectionParser,
        private CodeSnippetExtractor $codeSnippetExtractor,
    ) {
    }

    /**
     * @param list<PHPStanRulePackage> $packages
     * @return list<PHPStanRule>
     */
    public function scan(array $packages): array
    {
        $vendorRoot = base_path('vendor');

        $rules = [];
        foreach ($packages as $package) {
            $packageDir = $vendorRoot . '/' . $package->getPackage();
            $sections = $this->readmeSectionParser->parseSections($packageDir);

            foreach ($this->ruleFileFinder->findInPackage($packageDir) as $file) {
                $parsed = $this->ruleClassParser->parse($file);
                if (! $parsed instanceof ParsedRuleClass) {
                    continue;
                }

                $snippets = $this->codeSnippetExtractor->extractFor($parsed->getShortName(), $sections);

                $rules[] = new PHPStanRule(
                    package: $package->getPackage(),
                    class: $parsed->getFullyQualifiedName(),
                    name: $parsed->getShortName(),
                    message: $parsed->getMessage(),
                    description: $snippets->getDescription() !== '' ? $snippets->getDescription() : $parsed->getDocComment(),
                    wrongCode: $snippets->getWrongCode(),
                    correctCode: $snippets->getCorrectCode(),
                    tip: $parsed->getTip(),
                );
            }
        }

        usort(
            $rules,
            static fn (PHPStanRule $a, PHPStanRule $b): int =>
                $a->getPackage() <=> $b->getPackage() ?: $a->getName() <=> $b->getName()
        );

        return $rules;
    }
}
