<?php

namespace App\Logic;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Filesystem\File;
use Cake\Http\Exception\BadRequestException;

/**
 * Class SymbolsFileResource
 *
 * @package App\Logic
 */
class SymbolsFileResource implements SymbolsResource
{

    private $filePath;
    private $columnNames;

    /**
     * SymbolsFileResource constructor.
     *
     * @param string $companiesFilePath A path to file with companies info
     */
    public function __construct(string $companiesFilePath)
    {
        $file = new File($companiesFilePath);

        if (false === $file->exists()) {
            throw new BadRequestException('Cannot read Companies info file.');
        }

        $this->filePath = $companiesFilePath;
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
     * @param string $symbol A company symbol to find whole record
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

    /**
     * @return array
     */
    private function readSource(): array
    {
        if (($handle = fopen($this->filePath, 'rb')) === false) {
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
}
