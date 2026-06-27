<?php

declare(strict_types=1);

namespace HyperfTest\Feature;

use HyperfTest\HttpTestCase;


/**
 * @method array get(string $uri, array $data = [], array $headers = [])
 * @method array post(string $uri, array $data = [], array $headers = [])
 * @method array json(string $uri, array $data = [], array $headers = [])
 * @method ResponseInterface request(string $method, string $uri, array $options = [])
 */
class OrderTest extends HttpTestCase
{
  public function testIndexReturnsOk(): void
  {
    $response = $this->get('/orders');
    $this->assertArrayHasKey('data', $response);
  }

  public function testIndexReturnsSource(): void
  {
    $response = $this->get('/orders');
    $this->assertArrayHasKey('source', $response);
  }

  public function testStoreCreatesOrder(): void
  {
    $response = $this->post('/orders', [
      'product' => 'Camiseta Test',
      'price'   => 49.90,
    ]);

    $this->assertArrayHasKey('data', $response);
    $this->assertSame('Camiseta Test', $response['data']['product']);
    $this->assertSame(49.90, $response['data']['price']);
    $this->assertSame('pending', $response['data']['status']);
  }

  public function testStoreFailsWithoutProduct(): void
  {
    $response = $this->post('/orders', [
      'price' => 49.90,
    ]);

    $this->assertSame('Product is required', $response['message']);
  }

  public function testStoreFailsWithInvalidPrice(): void
  {
    $response = $this->post('/orders', [
      'product' => 'Camiseta',
      'price'   => 'not-a-number',
    ]);

    $this->assertSame('Price needs to be a number', $response['message']);
  }

  public function testShowReturnsOrder(): void
  {
    $created = $this->post('/orders', [
      'product' => 'Tênis Test',
      'price'   => 199.90,
    ]);

    $id = $created['data']['id'];
    $response = $this->get("/orders/{$id}");

    $this->assertArrayHasKey('data', $response);
    $this->assertSame($id, $response['data']['id']);
  }

  public function testShowReturns404ForInvalidId(): void
  {
    $response = $this->get('/orders/999999');
    $this->assertSame('Order not found', $response['message']);
  }

  public function testUpdateChangesStatus(): void
  {
    $created = $this->post('/orders', [
      'product' => 'Jaqueta Test',
      'price'   => 299.90,
    ]);

    $id = $created['data']['id'];

    $response = $this->request('PUT', "/orders/{$id}", [
      'json'    => ['status' => 'processing'],
      'headers' => ['Content-Type' => 'application/json'],
    ]);

    $body = json_decode($response->getBody()->getContents(), true);
    $this->assertSame('processing', $body['data']['status']);
  }

  public function testDeleteRemovesOrder(): void
  {
    $created = $this->post('/orders', [
      'product' => 'Calça Test',
      'price'   => 89.90,
    ]);

    $id = $created['data']['id'];

    $this->request('DELETE', "/orders/{$id}");

    $response = $this->get("/orders/{$id}");
    $this->assertSame('Order not found', $response['message']);
  }
}
