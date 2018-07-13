<?php

namespace App\Controller;

use App\Logic\StockDataLogic;
use Cake\Datasource\ResultSetInterface;
use Cake\Validation\Validator;

/**
 * Class StockController
 *
 * @package App\Controller
 * @method ResultSetInterface paginate($object = null, array $settings = [])
 */
class StockController extends AppController
{

    public function index()
    {
        if ($this->getRequest()->is('post')) {
            $validator = new Validator();

            $validator->requirePresence('symbol')
                ->notEmpty('symbol', 'Please fill this field')
                ->add('symbol', [
                    'length' => [
                        'rule' => ['minLength', 3],
                        'message' => 'Company Symbol need to be at least 3 characters long',
                    ],
                ]);

            $validator->requirePresence('start_date')
                ->notEmpty('start_date', 'Please fill this field')
                ->date('start_date', ['mdy']);

            $validator->requirePresence('end_date')
                ->notEmpty('end_date', 'Please fill this field')
                ->date('end_date', ['mdy']);

            $validator->requirePresence('email')
                ->notEmpty('email', 'Please fill this field')
                ->email('email');

            $errors = $validator->errors($this->request->getData());

            if (empty($errors)) {
                $stockData = (new StockDataLogic())->getStockData($this->getRequest()->getData());

                $stockData = $this->paginate($stockData);
                $this->set('stockData', $stockData);

                $this->render('show_data');
            }

            $this->Flash->error('Fix errors');
            $this->set('errors', $errors);
        }
    }

    public function showData()
    {

    }
}
