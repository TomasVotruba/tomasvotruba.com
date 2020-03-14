<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Posts\Year2018\ParameterToSymfonyController\Controller;

final class LectureController
{
    private string $bankAccount;

    public function __construct(string $bankAccount)
    {
        $this->bankAccount = $bankAccount;
    }

    /**
     * Just for testing purposes,
     * controller should not contain public methods except actions
     */
    public function getBankAccount(): string
    {
        return $this->bankAccount;
    }
}
