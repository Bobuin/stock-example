<?php

namespace App\Test\TestCase\Logic;

use App\Logic\SymbolsFileResource;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\TestSuite\TestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Class SymbolsFileResourceTest
 *
 * @package App\Test\TestCase\Logic
 */
class SymbolsFileResourceTest extends TestCase
{
    public function testInstantiationFail(): void
    {
        try {
            new SymbolsFileResource('empty' . DS . 'path');
            $this->assertTrue(false);
        } catch (\Exception $exception) {
            self::assertInstanceOf(BadRequestException::class, $exception);
            self::assertEquals('Cannot read Companies info file.', $exception->getMessage());
        }
    }

    public function testInstantiation(): void
    {
        vfsStream::setup('home');
        $file = vfsStream::url('home' . DS . 'test.csv');
        file_put_contents($file, 'The new contents of the file');

        $object = new SymbolsFileResource($file);

        self::assertInstanceOf(SymbolsFileResource::class, $object);
    }

    public function testGetSymbols()
    {
        vfsStream::setup('home');
        $file = vfsStream::url('home' . DS . 'test.csv');
        file_put_contents(
            $file,
            '"Symbol","Name","LastSale","MarketCap","IPOyear","Sector","industry","Summary Quote",
                "PIH","1347 Property Insurance Holdings, Inc.","6.95","$41.59M","2014","Finance","Property-Casualty Insurers","https://www.nasdaq.com/symbol/pih",
                "PIHPP","1347 Property Insurance Holdings, Inc.","25.75","n/a","n/a","Finance","Property-Casualty Insurers","https://www.nasdaq.com/symbol/pihpp",'
        );

        $symbols = (new SymbolsFileResource($file))->getSymbols();

        self::assertEquals(['PIH', 'PIHPP'], $symbols);
    }

    public function testFindRecordBySymbolFail()
    {
        vfsStream::setup('home');
        $file = vfsStream::url('home' . DS . 'test.csv');
        file_put_contents(
            $file,
            '"Symbol","Name","LastSale","MarketCap","IPOyear","Sector","industry","Summary Quote",
                "PIH","1347 Property Insurance Holdings, Inc.","6.95","$41.59M","2014","Finance","Property-Casualty Insurers","https://www.nasdaq.com/symbol/pih",
                "PIHPP","1347 Property Insurance Holdings, Inc.","25.75","n/a","n/a","Finance","Property-Casualty Insurers","https://www.nasdaq.com/symbol/pihpp",'
        );

        try {
            (new SymbolsFileResource($file))->findRecordBySymbol('GOOG');
            $this->assertTrue(false);
        } catch (\Exception $exception) {
            self::assertInstanceOf(RecordNotFoundException::class, $exception);
            self::assertEquals('No company info with this symbol', $exception->getMessage());
        }
    }

    public function testFindRecordBySymbol()
    {
        vfsStream::setup('home');
        $file = vfsStream::url('home' . DS . 'test.csv');
        file_put_contents(
            $file,
            '"Symbol","Name","LastSale","MarketCap","IPOyear","Sector","industry","Summary Quote"
                "PIH","1347 Property Insurance Holdings, Inc.","6.95","$41.59M","2014","Finance","Property-Casualty Insurers","https://www.nasdaq.com/symbol/pih"
                "PIHPP","1347 Property Insurance Holdings, Inc.","25.75","n/a","n/a","Finance","Property-Casualty Insurers","https://www.nasdaq.com/symbol/pihpp"'
        );

        $info = (new SymbolsFileResource($file))->findRecordBySymbol('PIH');

        $expect = [
            'Symbol' => 'PIH',
            'Name' => '1347 Property Insurance Holdings, Inc.',
            'LastSale' => '6.95',
            'MarketCap' => '$41.59M',
            'IPOyear' => '2014',
            'Sector' => 'Finance',
            'industry' => 'Property-Casualty Insurers',
            'Summary Quote' => 'https://www.nasdaq.com/symbol/pih',
        ];

        self::assertEquals($expect, $info);
    }
}
