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

    /**
     * @param string $symbol A company symbol to find whole record
     *
     * @return array
     */
    public function findRecordBySymbol(string $symbol): array;
}
