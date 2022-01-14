<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter;

final class Randomizer
{
    /**
     * @template T as object
     * @param T[] $items
     * @return T
     */
    public function resolveRandomItem(array $items): object
    {
        $randomKey = array_rand($items);
        return $items[$randomKey];
    }
}
