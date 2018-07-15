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
     * @param \App\Logic\SymbolsResource $symbolsResource The resource to get symbols info
     * @param null|string                $dataSource      The resource to get historical data
     */
    public function __construct(SymbolsResource $symbolsResource, ?string $dataSource = null)
    {
        $this->resource = $symbolsResource;

        if (null !== $dataSource) {
            $this->historicalDataResource = $dataSource;
        }
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
     * @param RequestData $requestData Data object with necessary request data
     *
     * @return \Cake\Datasource\ResultSetInterface
     */
    public function getStockData(RequestData $requestData): ResultSetInterface
    {
        $rawData = $this->getRawStockData($requestData);

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
    private function getRawStockData(RequestData $requestData): string
    {
        $link = preg_replace(
            ['/##symbol##/', '/##start_date##/', '/##end_date##/'],
            [$requestData->getSymbol(), $requestData->getStartDate(), $requestData->getEndDate()],
            $this->historicalDataResource
        );

        $rawData = file_get_contents($link);

        if (empty($rawData)) {
            throw new BadRequestException('Cannot retrieve stock data. Site quandl.com is unavailable.');
        }

        return $rawData;
    }
}
