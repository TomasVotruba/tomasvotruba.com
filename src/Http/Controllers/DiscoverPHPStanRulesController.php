<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\PHPStanRules\Repository\PHPStanRulePackageRepository;
use App\PHPStanRules\Repository\PHPStanRuleRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class DiscoverPHPStanRulesController extends Controller
{
    public function __construct(
        private readonly PHPStanRuleRepository $phpstanRuleRepository,
        private readonly PHPStanRulePackageRepository $phpstanRulePackageRepository,
    ) {
    }

    public function __invoke(): View
    {
        $groupedRules = $this->phpstanRuleRepository->fetchGroupedByPackage();

        return \view('discover-phpstan-rules', [
            'title' => 'Discover PHPStan Rules',
            'groupedRules' => $groupedRules,
            'totalCount' => array_sum(array_map(count(...), $groupedRules)),
            'generatedAt' => $this->phpstanRuleRepository->getGeneratedAt(),
            'packagesByName' => $this->phpstanRulePackageRepository->fetchAllByPackage(),
        ]);
    }
}
