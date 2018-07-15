<?php

namespace App\Logic;

use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenDate;

/**
 * Class RequestData
 *
 * @package App\Logic
 */
class RequestData
{
    private const DATE_FORMAT = 'Y-m-d';
    private const PARSE_DATE_FORMAT = 'Y-M-d';
    /** @var string */
    private $symbol;
    /** @var FrozenDate */
    private $startDate;
    /** @var FrozenDate */
    private $endDate;

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol The company's stock symbol
     *
     * @return void
     */
    public function setSymbol($symbol): void
    {
        $this->symbol = $symbol;
    }

    /**
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->startDate->format(self::DATE_FORMAT);
    }

    /**
     * @param string $startDate Start date of report interval
     *
     * @return void
     */
    public function setStartDate(string $startDate): void
    {
        $parsedStartDate = FrozenDate::parseDate($startDate, self::PARSE_DATE_FORMAT);

        if ($parsedStartDate === null) {
            throw new BadRequestException('Cannot parse start date.');
        }

        $this->startDate = $parsedStartDate;
    }

    /**
     * @return string
     */
    public function getEndDate(): string
    {
        return $this->endDate->format(self::DATE_FORMAT);
    }

    /**
     * @param string $endDate End date of report interval
     *
     * @return void
     */
    public function setEndDate(string $endDate): void
    {
        $parsedEndDate = FrozenDate::parseDate($endDate, self::PARSE_DATE_FORMAT);

        if ($parsedEndDate === null) {
            throw new BadRequestException('Cannot parse end date.');
        }

        $this->endDate = $parsedEndDate;
    }
}
