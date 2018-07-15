<?php

namespace App\Test\TestCase\Logic;

use App\Logic\RequestData;
use App\Logic\StockDataLogic;
use App\Logic\SymbolsFileResource;
use Cake\Http\Exception\BadRequestException;
use Cake\TestSuite\TestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Class StockDataLogicTest
 *
 * @package App\Test\TestCase\Logic
 */
class StockDataLogicTest extends TestCase
{

    public function testGetSymbolsList()
    {
        vfsStream::setup('home');
        $file = vfsStream::url('home/test.csv');
        file_put_contents(
            $file,
            '"Symbol","Name","LastSale","MarketCap","IPOyear","Sector","industry","Summary Quote",
                "PIH","1347 Property Insurance Holdings, Inc.","6.95","$41.59M","2014","Finance","Property-Casualty Insurers","https://www.nasdaq.com/symbol/pih",
                "PIHPP","1347 Property Insurance Holdings, Inc.","25.75","n/a","n/a","Finance","Property-Casualty Insurers","https://www.nasdaq.com/symbol/pihpp",'
        );

        $symbolsResource = new SymbolsFileResource($file);

        $stockDataLogic = new StockDataLogic($symbolsResource);

        $this->assertEquals(['PIH', 'PIHPP'], $stockDataLogic->getSymbolsList());
    }

    public function testGetCompanyName(): void
    {
        vfsStream::setup('home');
        $file = vfsStream::url('home/test.csv');
        file_put_contents(
            $file,
            '"Symbol","Name","LastSale","MarketCap","IPOyear","Sector","industry","Summary Quote",
                "PIH","1347 Property Insurance Holdings, Inc.","6.95","$41.59M","2014","Finance","Property-Casualty Insurers","https://www.nasdaq.com/symbol/pih",
                "PIHPP","1347 Property Insurance Holdings, Inc.","25.75","n/a","n/a","Finance","Property-Casualty Insurers","https://www.nasdaq.com/symbol/pihpp",'
        );

        $stockDataLogic = new StockDataLogic(new SymbolsFileResource($file));

        $this->assertEquals('1347 Property Insurance Holdings, Inc.', $stockDataLogic->getCompanyName('PIHPP'));
    }

    public function testGetStockDataFail()
    {
        vfsStream::setup('home');

        $symbolsSource = vfsStream::url('home/test.csv');
        file_put_contents($symbolsSource, 'The new contents of the file');
        $dataPath = vfsStream::url('home/data.csv');
        file_put_contents($dataPath, '');

        $stockDataLogic = new StockDataLogic(new SymbolsFileResource($symbolsSource), $dataPath);

        $requestDataMock = $this->createMock(RequestData::class);
        $requestDataMock->method('getSymbol')->willReturn('GOOG');
        $requestDataMock->method('getStartDate')->willReturn('2017-01-01');
        $requestDataMock->method('getEndDate')->willReturn('2017-03-06');

        try {
            $stockDataLogic->getStockData($requestDataMock);
            $this->assertTrue(false);
        } catch (\Exception $exception) {
            self::assertInstanceOf(BadRequestException::class, $exception);
            self::assertEquals('Cannot retrieve stock data. Site quandl.com is unavailable.', $exception->getMessage());
        }
    }

    public function testGetStockData()
    {
        vfsStream::setup('home');

        $symbolsSource = vfsStream::url('home/test.csv');
        file_put_contents(
            $symbolsSource,
            '"Symbol","Name","LastSale","MarketCap","IPOyear","Sector","industry","Summary Quote",
                "PIH","1347 Property Insurance Holdings, Inc.","6.95","$41.59M","2014","Finance","Property-Casualty Insurers","https://www.nasdaq.com/symbol/pih",
                "PIHPP","1347 Property Insurance Holdings, Inc.","25.75","n/a","n/a","Finance","Property-Casualty Insurers","https://www.nasdaq.com/symbol/pihpp",'
        );

        $symbol = 'GOOG';
        $startDate = '2017-01-01';
        $endDate = '2017-03-06';

        $rawData = 'Date,Open,High,Low,Close,Volume,Ex-Dividend,Split Ratio,Adj. Open,Adj. High,Adj.' .
            ' Low,Adj. Close,Adj. Volume' . PHP_EOL .
            '2017-01-03,778.81,789.63,775.8,786.14,1657268.0,0.0,1.0,778.81,789.63,775.8,786.14,1657268.0' . PHP_EOL .
            '2017-01-04,788.36,791.34,783.16,786.9,1072958.0,0.0,1.0,788.36,791.34,783.16,786.9,1072958.0';

        $file = vfsStream::url('home' . DS . $symbol . '_' . $startDate . '_' . $endDate . '.csv');
        file_put_contents($file, $rawData);

        $dataSource = 'vfs://home/##symbol##_##start_date##_##end_date##.csv';

        $stockDataLogic = new StockDataLogic(new SymbolsFileResource($symbolsSource), $dataSource);

        $requestDataMock = $this->createMock(RequestData::class);
        $requestDataMock->method('getSymbol')->willReturn($symbol);
        $requestDataMock->method('getStartDate')->willReturn($startDate);
        $requestDataMock->method('getEndDate')->willReturn($endDate);

        $result = $stockDataLogic->getStockData($requestDataMock);

        self::assertCount(2, $result);
    }
}
