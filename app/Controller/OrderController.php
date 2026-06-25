<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Order;
use Hyperf\Context\ApplicationContext;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Redis\Redis;
use App\Event\OrderCreated;
use OpenApi\Attributes as OA;
use Psr\EventDispatcher\EventDispatcherInterface;

#[OA\Info(title: 'Hyperf Orders API', version: '1.0.0')]
class OrderController
{
    public function index(ResponseInterface $response)
    {
        $redis = ApplicationContext::getContainer()->get(Redis::class);
        $cached = $redis->get('orders:all');
        if ($cached) {
            return $response->json(['data' => json_decode($cached, true), 'source' => 'cache']);
        }
        $orders = Order::all();
        $redis->set('orders:all', json_encode($orders), 60);
        return $response->json(['data' => $orders, 'source' => 'database']);
    }

    public function show(int $id, ResponseInterface $response)
    {
        $order = Order::find($id);
        if (!$order) {
            return $response->json(['message' => 'Order not found'])->withStatus(404);
        }
        return $response->json(['data' => $order]);
    }

    public function store(RequestInterface $request, ResponseInterface $response)
    {
        $data = $request->all();
        if (empty($data['product'])) {
            return $response->json(['message' => 'Product is required'])->withStatus(422);
        }
        if (empty($data['price']) || !is_numeric($data['price'])) {
            return $response->json(['message' => 'Price needs to be a number'])->withStatus(422);
        }
        $order = Order::create([
            'product' => $data['product'],
            'price'   => (float) $data['price'],
            'status'  => $data['status'] ?? 'pending',
        ]);
        $dispatcher = ApplicationContext::getContainer()->get(EventDispatcherInterface::class);
        $dispatcher->dispatch(new OrderCreated($order));
        $redis = ApplicationContext::getContainer()->get(Redis::class);
        $redis->del('orders:all');
        return $response->json(['data' => $order, 'message' => 'Order created'])->withStatus(201);
    }

    public function update(int $id, RequestInterface $request, ResponseInterface $response)
    {
        $order = Order::find($id);
        if (!$order) {
            return $response->json(['message' => 'Order not found'])->withStatus(404);
        }
        $order->fill($request->all());
        $order->save();
        $redis = ApplicationContext::getContainer()->get(Redis::class);
        $redis->del('orders:all');
        $redis->del("orders:{$id}");
        return $response->json(['data' => $order, 'message' => 'Order updated']);
    }

    public function delete(int $id, ResponseInterface $response)
    {
        $order = Order::find($id);
        if (!$order) {
            return $response->json(['message' => 'Order not found'])->withStatus(404);
        }
        $order->delete();
        $redis = ApplicationContext::getContainer()->get(Redis::class);
        $redis->del('orders:all');
        $redis->del("orders:{$id}");
        return $response->json(['message' => 'Order deleted']);
    }
}
