<?php

namespace App\Test\TestCase\Logic;

use App\Logic\RequestData;
use Cake\Http\Exception\BadRequestException;
use Cake\TestSuite\TestCase;

/**
 * Class RequestDataTest
 *
 * @package App\Test\TestCase\Logic
 */
class RequestDataTest extends TestCase
{
    public function testGetSymbol()
    {
        $symbol = 'BLA';

        $object = new RequestData();
        $object->setSymbol($symbol);

        $this->assertEquals($symbol, $object->getSymbol());
    }

    /**
     * Test setStartDate method
     */
    public function testSetStartDateFail(): void
    {
        $date = 'BLA';

        $object = new RequestData();
        try {
            $object->setStartDate($date);
            $this->assertTrue(false);
        } catch (\Exception $exception) {
            self::assertEquals('Cannot parse start date.', $exception->getMessage());
        }
    }

    /**
     * Test getStartDate method
     */
    public function testGetStartDate(): void
    {
        $date = '2017-01-10';

        $object = new RequestData();
        $object->setStartDate($date);

        $this->assertEquals('2017-01-10', $object->getStartDate());
    }

    /**
     * Test setEndDate method
     */
    public function testSetEndDate(): void
    {
        $date = 'BLA';

        $object = new RequestData();
        try {
            $object->setEndDate($date);
            $this->assertTrue(false);
        } catch (\Exception $exception) {
            self::assertInstanceOf(BadRequestException::class, $exception);
            self::assertEquals('Cannot parse end date.', $exception->getMessage());
        }
    }

    /**
     * Test getEndDate method
     */
    public function testGetEndDate(): void
    {
        $date = '2017-01-10';

        $object = new RequestData();
        $object->setEndDate($date);

        $this->assertEquals('2017-01-10', $object->getEndDate());
    }
}
