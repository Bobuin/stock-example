<?php

namespace App\Logic;

use Cake\I18n\FrozenDate;

/**
 * Class RequestData
 *
 * @package App\Logic
 */
class RequestData
{
    private const DATE_FORMAT = 'Y-m-d';
    private $originalData;
    private $symbol;
    /** @var FrozenDate */
    private $startDate;
    /** @var FrozenDate */
    private $endDate;

    /**
     * RequestData constructor.
     *
     * @param array|null $data
     */
    public function __construct(?array $data = null)
    {
        $this->originalData = $data;
    }

    /**
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->startDate->format(self::DATE_FORMAT);
    }

    /**
     * @param string $startDate
     */
    public function setStartDate(string $startDate): void
    {
        $this->startDate = FrozenDate::parseDate($startDate);
    }

    /**
     * @return string
     */
    public function getEndDate(): string
    {
        return $this->endDate->format(self::DATE_FORMAT);
    }

    /**
     * @param string $endDate
     */
    public function setEndDate(string $endDate): void
    {
        $this->endDate = FrozenDate::parseDate($endDate);
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     */
    public function setSymbol($symbol): void
    {
        $this->symbol = $symbol;
    }
}
