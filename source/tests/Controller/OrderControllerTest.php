<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderControllerTest extends WebTestCase
{
    /**
     * Test various invalid order creation scenarios
     */
    public function testCreateOrderWithInvalidInputs()
    {
        $client = static::createClient();

        // 1️ Invalid format: "A,B,C" (API expects string like "ABC")
        $client->request('POST', '/api/v1/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'items' => 'A,B,C'
        ]));
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        echo "\n🚨 Invalid Format Test: Expected 400, Got " . $response->getStatusCode();
        dump($responseData);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), 'Expected HTTP 400 Bad Request for invalid format');

        // 2️ Invalid SKU: "INVALID_ITEM"
        $client->request('POST', '/api/v1/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'items' => 'INVALID_ITEM'
        ]));
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        echo "\n🚨 Invalid SKU Test: Expected 400, Got " . $response->getStatusCode();
        dump($responseData);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), 'Expected HTTP 400 Bad Request for invalid SKU');

        // 3️ Items as Array (invalid type)
        $client->request('POST', '/api/v1/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'items' => []
        ]));
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        echo "\n🚨 Items as Array Test: Expected 400, Got " . $response->getStatusCode();
        dump($responseData);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), 'Expected HTTP 400 Bad Request for non-string items');

        // 4️ Comma-Separated SKUs
        $client->request('POST', '/api/v1/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'items' => "A,V,C,Z"
        ]));
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        echo "\n🚨 Comma-Separated SKUs Test: Expected 400, Got " . $response->getStatusCode();
        dump($responseData);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), 'Expected HTTP 400 Bad Request for comma-separated SKUs');

        //Valid format
        //1 Valid input (should return 201 CREATED)
        $client->request('POST', '/api/v1/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'items' => "AA"
        ]));
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        echo "\n✅ Valid SKU Test (AA): Expected 201, Got " . $response->getStatusCode();
        dump($responseData);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), 'Expected HTTP 201 Created for valid SKU');

        // 2. Valid input (should return 201 CREATED)
        $client->request('POST', '/api/v1/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'items' => "ABCD"
        ]));
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        echo "\n✅ Valid SKU Test (ABCD): Expected 201, Got " . $response->getStatusCode();
        dump($responseData);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), 'Expected HTTP 201 Created for valid SKU');

    }

}