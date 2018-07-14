<?php

namespace App\Controller;

use App\Logic\RequestData;
use App\Logic\StockDataLogic;
use App\Logic\SymbolsFileResource;
use Cake\Validation\Validator;

/**
 * Class StockController
 *
 * @package App\Controller
 */
class StockController extends AppController
{

    public function index()
    {
        if ($this->getRequest()->is('post')) {
            $stockDataLogic = new StockDataLogic(new SymbolsFileResource());
            $symbolsList = $stockDataLogic->getSymbolsList();

            $validator = new Validator();

            $validator->requirePresence('symbol')
                ->notEmpty('symbol', 'Please fill this field')
                ->inList('symbol', $symbolsList, 'Company Symbol must be valid Stock code.');

            $validator->requirePresence('start_date')
                ->notEmpty('start_date', 'Please fill this field')
                ->date('start_date', ['mdy']);

            $validator->requirePresence('end_date')
                ->notEmpty('end_date', 'Please fill this field')
                ->date('end_date', ['mdy'])
                ->greaterThanOrEqualToField('end_date', 'start_date');

            $validator->requirePresence('email')
                ->notEmpty('email', 'Please fill this field')
                ->email('email');

            $errors = $validator->errors($this->request->getData());

            if (empty($errors)) {
                $requestData = new RequestData($this->request->getData());

                $requestData->setSymbol($this->request->getData('symbol'));
                $requestData->setStartDate($this->request->getData('start_date'));
                $requestData->setEndDate($this->request->getData('end_date'));

                $stockData = $stockDataLogic->getStockData($requestData);

                $companyName = $stockDataLogic->getCompanyName($requestData->getSymbol());

                \Cake\Mailer\Email::deliver(
                    $this->request->getData('email'),
                    $companyName,
                    'From '. $requestData->getStartDate() . ' to '. $requestData->getEndDate(),
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
}
