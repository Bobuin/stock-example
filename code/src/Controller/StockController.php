<?php

namespace App\Controller;

use App\Logic\RequestData;
use App\Logic\StockDataLogic;
use App\Logic\SymbolsFileResource;
use Cake\Mailer\Email;
use Cake\Validation\Validator;

/**
 * Class StockController
 *
 * @package App\Controller
 */
class StockController extends AppController
{

    /**
     * Main action
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        if ($this->getRequest()->is('post')) {
            $stockDataLogic = new StockDataLogic(new SymbolsFileResource());

            $symbolsList = $stockDataLogic->getSymbolsList();

            $errors = $this->inputValidation($symbolsList);

            if (empty($errors)) {
                $requestData = $this->prepareRequestData();

                $rawStockData = $stockDataLogic->getRawStockData($requestData);

                $stockData = $stockDataLogic->formatStockData($rawStockData);

                $companyName = $stockDataLogic->getCompanyName($requestData->getSymbol());

                Email::deliver(
                    $this->request->getData('email'),
                    $companyName,
                    'From ' . $requestData->getStartDate() . ' to ' . $requestData->getEndDate(),
                    ['from' => 'stock@example.com']
                );

                $this->set('stockData', $stockData);

                $this->render('show_data');
            } else {
                $this->Flash->error('Fix errors');
                $this->set('errors', $errors);
            }
        }
    }

    /**
     * @param array $symbolsList All available symbols
     *
     * @return array
     */
    private function inputValidation(array $symbolsList): array
    {
        $validator = new Validator();

        $validator->requirePresence('symbol')
            ->notEmpty('symbol', 'Please fill this field')
            ->inList('symbol', $symbolsList, 'Company Symbol must be valid Stock code.');

        $validator->requirePresence('start_date')
            ->notEmpty('start_date', 'Please fill this field')
            ->date('start_date', ['ymd']);

        $validator->requirePresence('end_date')
            ->notEmpty('end_date', 'Please fill this field')
            ->date('end_date', ['ymd'])
            ->greaterThanOrEqualToField('end_date', 'start_date');

        $validator->requirePresence('email')
            ->notEmpty('email', 'Please fill this field')
            ->email('email');

        /** @var array $data */
        $data = $this->request->getData();

        return $validator->errors($data);
    }

    /**
     * @return RequestData
     */
    private function prepareRequestData(): RequestData
    {
        $requestData = new RequestData();

        /** @var string $symbol */
        $symbol = $this->request->getData('symbol');
        /** @var string $startDate */
        $startDate = $this->request->getData('start_date');
        /** @var string $endDate */
        $endDate = $this->request->getData('end_date');

        $requestData->setSymbol($symbol);
        $requestData->setStartDate($startDate);
        $requestData->setEndDate($endDate);

        return $requestData;
    }
}
