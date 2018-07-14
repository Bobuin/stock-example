<?php

namespace App\Logic;

/**
 * Interface SymbolsResource
 *
 * @package App\Logic
 */
interface SymbolsResource
{
    /**
     * @return array
     */
    public function getSymbols(): array;

    public function findRecordBySymbol(string $symbol): array;
}
