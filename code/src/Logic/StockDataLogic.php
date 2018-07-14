<?php

namespace App\Logic;

use Cake\Datasource\ResultSetDecorator;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Exception\BadRequestException;

/**
 * Class StockDataLogic
 *
 * @package App\Logic
 */
class StockDataLogic
{

    private $resource;

    private $historicalDataResource = 'https://www.quandl.com/api/v3/datasets/WIKI/##symbol##.csv?order=asc' .
    '&start_date=##start_date##&end_date=##end_date##';

    /**
     * StockDataLogic constructor.
     *
     * @param \App\Logic\SymbolsResource $resource The resource to get symbols info
     */
    public function __construct(SymbolsResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return array
     */
    public function getSymbolsList(): array
    {
        return $this->resource->getSymbols();
    }

    /**
     * @param string $symbol A company symbol to find full name
     *
     * @return string
     */
    public function getCompanyName(string $symbol): string
    {
        $record = $this->resource->findRecordBySymbol($symbol);

        return $record['Name'];
    }

    /**
     * @param string $rawData Retrieved data from stock source
     *
     * @return \Cake\Datasource\ResultSetInterface
     */
    public function formatStockData(string $rawData): ResultSetInterface
    {
        $explodedData = explode(PHP_EOL, trim($rawData));

        $data = [];
        foreach ($explodedData as $row) {
            $data[] = str_getcsv($row);
        }
        array_shift($data);

        return new ResultSetDecorator($data);
    }

    /**
     * @param RequestData $requestData Data object with necessary request data
     *
     * @return string
     */
    public function getRawStockData(RequestData $requestData): string
    {
        $link = preg_replace(
            ['/##symbol##/', '/##start_date##/', '/##end_date##/'],
            [$requestData->getSymbol(), $requestData->getStartDate(), $requestData->getEndDate()],
            $this->historicalDataResource
        );

        $rawData = file_get_contents($link);

        if (false === $rawData) {
            throw new BadRequestException('Cannot retrieve stock data. Site quandl.com unavailable.');
        }

        return $rawData;
    }
}
