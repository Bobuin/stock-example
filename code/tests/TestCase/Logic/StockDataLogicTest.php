<?php

namespace App\Test\TestCase\Logic;

use App\Logic\StockDataLogic;
use App\Logic\SymbolsFileResource;
use Cake\TestSuite\TestCase;

/**
 * Class StockDataLogicTest
 *
 * @package App\Test\TestCase\Logic
 */
class StockDataLogicTest extends TestCase
{

    public function testGetSymbolsList()
    {
        $symbolsResourceMock = $this->createMock(SymbolsFileResource::class);

        $symbolsResourceMock->method('getSymbols')->willReturn(['CODE1', 'CODE2']);

        $stockDataLogic = new StockDataLogic($symbolsResourceMock);

        $this->assertEquals(['CODE1', 'CODE2'], $stockDataLogic->getSymbolsList());
    }

    public function testFormatStockData()
    {
        $symbolsResourceMock = $this->createMock(SymbolsFileResource::class);
        $stockDataLogic = new StockDataLogic($symbolsResourceMock);

        $rawData = 'Date,Open,High,Low,Close,Volume,Ex-Dividend,Split Ratio,Adj. Open,Adj. High,Adj.' .
            ' Low,Adj. Close,Adj. Volume' . PHP_EOL .
            '2017-01-03,778.81,789.63,775.8,786.14,1657268.0,0.0,1.0,778.81,789.63,775.8,786.14,1657268.0' . PHP_EOL .
            '2017-01-04,788.36,791.34,783.16,786.9,1072958.0,0.0,1.0,788.36,791.34,783.16,786.9,1072958.0';

        $result = $stockDataLogic->formatStockData($rawData);
        self::assertCount(2, $result);
    }

    public function testGetCompanyName(): void
    {
        $symbolsResourceMock = $this->createMock(SymbolsFileResource::class);

        $symbolsResourceMock->method('findRecordBySymbol')->willReturn(
            ['Symbol' => 'CODE1', 'Name' => 'Company Name']
        );

        $stockDataLogic = new StockDataLogic($symbolsResourceMock);

        $this->assertEquals('Company Name', $stockDataLogic->getCompanyName('CODE1'));
    }
}
