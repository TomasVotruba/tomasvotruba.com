<?php

declare(strict_types=1);

namespace App\PHPStanRules\Command;

use App\PHPStanRules\InstalledRulePackageFilter;
use App\PHPStanRules\PHPStanRuleScanner;
use App\PHPStanRules\Repository\PHPStanRulePackageRepository;
use App\PHPStanRules\ValueObject\PHPStanRule;
use Illuminate\Console\Command;
use RuntimeException;

final class ScanPHPStanRulesCommand extends Command
{
    private const string OUTPUT_FILENAME = 'discover-phpstan-rules.json';

    protected $signature = 'app:scan-phpstan-rules';

    protected $description = 'Scan installed PHPStan rule packages and write resources/discover-phpstan-rules.json';

    public function __construct(
        private readonly PHPStanRulePackageRepository $phpstanRulePackageRepository,
        private readonly InstalledRulePackageFilter $installedRulePackageFilter,
        private readonly PHPStanRuleScanner $phpstanRuleScanner,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $packages = $this->phpstanRulePackageRepository->fetchAllByPackage();
        } catch (RuntimeException $runtimeException) {
            $this->error($runtimeException->getMessage());
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
            static fn (PHPStanRule $phpStanRule): bool => $phpStanRule->getWrongCode() !== '' || $phpStanRule->getCorrectCode() !== '',
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
    private function toJsonShape(PHPStanRule $phpStanRule): array
    {
        return [
            'package' => $phpStanRule->getPackage(),
            'class' => $phpStanRule->getClass(),
            'name' => $phpStanRule->getName(),
            'message' => $phpStanRule->getMessage(),
            'description' => $phpStanRule->getDescription(),
            'wrong_code' => $phpStanRule->getWrongCode(),
            'correct_code' => $phpStanRule->getCorrectCode(),
            'tip' => $phpStanRule->getTip(),
        ];
    }
}
