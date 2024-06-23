<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class InvocieTest extends TestCase
{
    protected function getBaseURL() {
        return 'http://localhost:8080/api/v1';
    }

    public function testSaveInvoiceData(): void
    {
        $postData = [
            'amount'=> '100',
            'due_date' => '2024-07-10'
        ];
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $this->getBaseURL()."/invoices");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $this->assertEquals(201, $response['code']);
        $this->assertTrue(true);
    }

    public function testGetInvoiceData(): void
    {
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $this->getBaseURL()."/invoices");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $this->assertEquals(200, $response['code']);
        $this->assertTrue(true);
    }

    public function testUpdatePaymentData(): void
    {
        $postData = [
            'amount'=> '10'
        ];
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $this->getBaseURL()."/invoices/2/payments");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $this->assertEquals(201, $response['code']);
        $this->assertTrue(true);
    }

    public function testProcessPendingInvoiceData(): void
    {
        $postData = [
            "late_fee"=> 10.5,
            "overdue_days"=> 10
        ];
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $this->getBaseURL()."/invoices/process-overdue");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $this->assertEquals(201, $response['code']);
        $this->assertTrue(true);
    }
}
