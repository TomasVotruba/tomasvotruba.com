<?php declare(strict_types=1);

namespace TomasVotruba\Website\Command;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\FileSystem\GeneratedFilesDumper;
use TomasVotruba\Website\ArrayUtils;
use TomasVotruba\Website\Packagist\PackageMonthlyDownloadsProvider;
use TomasVotruba\Website\Packagist\VendorPackagesProvider;
use TomasVotruba\Website\Statistics;

final class GeneratePackageStatsCommand extends Command
{
    /**
     * @var string[]
     */
    private $frameworkVendorToName = [
        'nette' => 'Nette',
        'symfony' => 'Symfony',
        // includes also laravel/framework
        'illuminate' => 'Laravel',
        'cakephp' => 'CakePHP',
        // single monorepos
        'zendframework' => 'Zend',
        'yiisoft' => 'Yii',
        // microframeworks
        'slim' => 'Slim',
        'silex' => 'Silex',

        // didn't pass the 1000 daily downloads minimal entrance
        // 'codeigniter' => 'Code Igniter', (900)
        // 'fuel' => 'FuelPHP', (618)
        // 'phalcon' => 'Phalcon', (15)
    ];

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var GeneratedFilesDumper
     */
    private $generatedFilesDumper;

    /**
     * @var PackageMonthlyDownloadsProvider
     */
    private $packageMonthlyDownloadsProvider;

    /**
     * @var VendorPackagesProvider
     */
    private $vendorPackagesProvider;

    /**
     * @var ArrayUtils
     */
    private $arrayUtils;

    /**
     * @var Statistics
     */
    private $statistics;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        GeneratedFilesDumper $generatedFilesDumper,
        PackageMonthlyDownloadsProvider $packageMonthlyDownloadsProvider,
        VendorPackagesProvider $vendorPackagesProvider,
        ArrayUtils $arrayUtils,
        Statistics $statistics
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->generatedFilesDumper = $generatedFilesDumper;
        $this->packageMonthlyDownloadsProvider = $packageMonthlyDownloadsProvider;
        $this->vendorPackagesProvider = $vendorPackagesProvider;
        $this->arrayUtils = $arrayUtils;
        $this->statistics = $statistics;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generates downloads stats data for PHP frameworks');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vendorData = $this->createVendorData();
        $this->generatedFilesDumper->dump('php_framework_trends', $vendorData);
        $this->symfonyStyle->success('Data imported!');

        return ShellCode::SUCCESS;
    }

    /**
     * @return mixed[]
     */
    private function createVendorData(): array
    {
        $vendorData = [];

        foreach ($this->frameworkVendorToName as $vendorName => $frameworkName) {
            $this->symfonyStyle->title(sprintf('Loading data for "%s" vendor', $vendorName));

            $vendorPackageNames = $this->vendorPackagesProvider->provideForVendor($vendorName);
            $packagesData = $this->createPackagesData($vendorPackageNames);

            $vendorTotalLastMonth = $this->arrayUtils->getArrayKeySum(
                $packagesData,
                'last_month_average_daily_downloads'
            );
            $vendorTotalLastYear = $this->arrayUtils->getArrayKeySum($packagesData, 'last_year_total');
            $averageLastYearTrend = $this->arrayUtils->getArrayKeyAverage($packagesData, 'last_year_trend');

            $vendorData[$vendorName] = [
                'name' => $frameworkName,
                // totals
                'vendor_total_last_month' => $vendorTotalLastMonth,
                'vendor_total_last_year' => $vendorTotalLastYear,
                'average_last_year_trend' => $averageLastYearTrend,
                // packages details
                'packages_data' => $packagesData,
            ];
        }

        $vendorData = $this->sortDataByKey($vendorData, 'average_last_year_trend');

        // metadata
        $data['vendors'] = $vendorData;
        $data['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');

        return $data;
    }

    private function createPackagesData(array $packageNames): array
    {
        $packagesData = [];

        foreach ($packageNames as $packageName) {
            $monthlyDownloads = $this->packageMonthlyDownloadsProvider->provideForPackage($packageName);

            // no data
            if (! isset($monthlyDownloads[0])) {
                continue;
            }

            $lastMonthDailyDownloads = $monthlyDownloads[0];

            // too small package → skip it
            if ($lastMonthDailyDownloads <= 1000) {
                continue;
            }

            // package younger than 12 months → skip it
            if (! isset($monthlyDownloads[11])) {
                continue;
            }

            $lastYearTrend = $this->statistics->resolveTrend($monthlyDownloads, 12);
            if ($lastYearTrend === null) {
                continue;
            }

            // too fresh package → skip it
            if ($lastYearTrend > 500) {
                continue;
            }

            $packageData = [
                'package_name' => $packageName,
                'last_month_average_daily_downloads' => $lastMonthDailyDownloads,
                'last_year_trend' => $lastYearTrend,
                'last_year_total' => $this->statistics->resolveTotal($monthlyDownloads, 12),
            ];

            $packageKey = $this->createPackageKey($packageName);
            $packagesData[$packageKey] = $packageData;
        }

        return $this->sortDataByKey($packagesData, 'last_year_trend');
    }

    private function sortDataByKey(array $data, string $key): array
    {
        usort($data, function (array $firstItem, array $secondItem) use ($key) {
            return $secondItem[$key] <=> $firstItem[$key];
        });

        return $data;
    }

    private function createPackageKey(string $packageName): string
    {
        return Strings::replace($packageName, '#(/|-)#', '_');
    }
}
