<?php

namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\StockController Test Case
 */
class StockControllerTest extends IntegrationTestCase
{
    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->get('/stock');
        $this->assertResponseOk();
        $this->assertResponseCode(200);
        $this->assertResponseContains('Input params');
    }

    /**
     * Test that missing template renders 404 page in production
     *
     * @return void
     */
    public function testMissingTemplate(): void
    {
        Configure::write('debug', false);
        $this->get('/stock/not_existing');

        $this->assertResponseError();
        $this->assertResponseContains('Error');
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndexPost(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/stock');

        $this->assertResponseOk();
        $this->assertResponseCode(200);
        $this->assertResponseContains('Input params');
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndexPostWrongData(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->post('/stock', ['sybmol']);

        $this->assertResponseOk();
        $this->assertResponseCode(200);
        $this->assertResponseContains('Company Symbol: This field is required');
        $this->assertResponseContains('Start Date: This field is required');
        $this->assertResponseContains('End Date: This field is required');
        $this->assertResponseContains('Email: This field is required');
    }
}
