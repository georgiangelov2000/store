<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderControllerTest extends WebTestCase
{
    /**
     * Test successful order creation with valid items
     */
    public function testCreateOrderSuccessfully()
    {
        $client = static::createClient();

        // Send a request with valid items
        $client->request('POST', '/api/v1/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'items' => 'A,B,C'
        ]));

        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // Check HTTP response status
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), 'Expected HTTP 201 Created');

        // Debugging Output
        dump('✅ Order Created Response:', $responseData);

        // Ensure the response contains necessary keys
        $this->assertArrayHasKey('order_id', $responseData, 'Missing "order_id" in response');
        $this->assertArrayHasKey('total_price', $responseData, 'Missing "total_price" in response');
        $this->assertEquals('created', $responseData['status'], 'Order status should be "created"');
    }

    /**
     * Test multiple order requests including both valid and invalid cases
     */
    public function testMultipleOrderRequests()
    {
        $client = static::createClient();

        // First request: Invalid item
        $client->request('POST', '/api/v1/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'items' => 'INVALID_ITEM'
        ]));

        $response1 = $client->getResponse();
        $responseData1 = json_decode($response1->getContent(), true);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response1->getStatusCode(), 'Expected HTTP 400 Bad Request for invalid item');
        $this->assertArrayHasKey('errors', $responseData1, 'Error response missing "errors" key');
        dump('🚨 Invalid Order Response:', $responseData1);

        // ✅ Second request: Valid items
        $client->request('POST', '/api/v1/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'items' => 'A,B,C'
        ]));

        $response2 = $client->getResponse();
        $responseData2 = json_decode($response2->getContent(), true);
        $this->assertEquals(Response::HTTP_CREATED, $response2->getStatusCode(), 'Expected HTTP 201 Created for valid order');
        $this->assertArrayHasKey('order_id', $responseData2, 'Missing "order_id" in valid response');
        $this->assertArrayHasKey('total_price', $responseData2, 'Missing "total_price" in valid response');
        dump('✅ Valid Order Response:', $responseData2);
    }

    /**
     * Test creating an order with an empty items array (edge case)
     */
    public function testCreateOrderWithEmptyItems()
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'items' => []
        ]));

        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), 'Expected HTTP 400 for empty items');
        $this->assertArrayHasKey('errors', $responseData, 'Missing "errors" key in response');
        dump('🚨 Empty Items Response:', $responseData);
    }

    /**
     * Test creating an order with missing JSON payload (invalid request)
     */
    public function testCreateOrderWithMissingPayload()
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/orders', [], [], ['CONTENT_TYPE' => 'application/json'], '');

        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), 'Expected HTTP 400 for missing payload');
        $this->assertArrayHasKey('errors', $responseData, 'Missing "errors" key in response');
        dump('🚨 Missing Payload Response:', $responseData);
    }

    /**
     * Test creating an order with invalid JSON structure
     */
    public function testCreateOrderWithInvalidJson()
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/orders', [], [], ['CONTENT_TYPE' => 'application/json'], '{invalid_json}');

        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), 'Expected HTTP 400 for invalid JSON');
        $this->assertArrayHasKey('errors', $responseData, 'Missing "errors" key in response');
        dump('🚨 Invalid JSON Response:', $responseData);
    }
}