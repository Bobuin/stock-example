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

    private $historicalDataResource = 'https://www.quandl.com/api/v3/datasets/WIKI/##symbol##.csv?' .
    'order=asc&start_date=##start_date##&end_date=##end_date##';
    private $resource;

    /**
     * StockDataLogic constructor.
     *
     * @param \App\Logic\SymbolsResource $resource
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
     * @param string $symbol
     *
     * @return string
     */
    public function getCompanyName(string $symbol): string
    {
        $record = $this->resource->findRecordBySymbol($symbol);

        return $record['Name'];
    }

    /**
     * @param RequestData $requestData
     *
     * @return \Cake\Datasource\ResultSetInterface
     */
    public function getStockData(RequestData $requestData): ResultSetInterface
    {
        $link = preg_replace(
            ['/##symbol##/', '/##start_date##/', '/##end_date##/'],
            [$requestData->getSymbol(), $requestData->getStartDate(), $requestData->getEndDate()],
            $this->historicalDataResource
        );

        $plainData = file_get_contents($link);

        if (false === $plainData) {
            throw new BadRequestException('Cannot retrieve stock data');
        }

        $explodedData = explode(PHP_EOL, trim($plainData));

        $data = [];
        foreach ($explodedData as $row) {
            $data[] = str_getcsv($row);
        }
        array_shift($data);

        return new ResultSetDecorator($data);
    }
}
