<?php

namespace App\Logic;

use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Class SymbolsFileResource
 *
 * @package App\Logic
 */
class SymbolsFileResource implements SymbolsResource
{

    private const RESOURCE_PATH = APP . 'Data' . DS . 'companylist.csv';

    /**
     * @return array
     */
    private $columnNames;

    /**
     * @return array
     */
    private function readSource(): array
    {
        if (($handle = fopen(self::RESOURCE_PATH, 'rb')) === false) {
            throw new RecordNotFoundException('Companies list file cannot be read.');
        }

        $fileContent = [];
        while (($rowData = fgetcsv($handle, 1000, ',')) !== false) {
            $fileContent[] = $rowData;
        }

        fclose($handle);

        $this->columnNames = array_shift($fileContent);

        return $fileContent;
    }

    /**
     * @return array
     */
    public function getSymbols(): array
    {
        $wholeData = $this->readSource();

        return array_column($wholeData, 0);
    }

    /**
     * @param string $symbol
     *
     * @return array
     */
    public function findRecordBySymbol(string $symbol): array
    {
        $wholeData = $this->readSource();

        foreach ($wholeData as $company) {
            if ($company[0] === $symbol) {
                return array_combine($this->columnNames, $company);
            }
        }

        throw new RecordNotFoundException('No company info with this symbol');
    }
}
