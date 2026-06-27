<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\OrderCreated;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
class OrderCreatedListener implements ListenerInterface
{
  public function listen(): array
  {
    return [
      OrderCreated::class,
    ];
  }

  public function process(object $event): void
  {
    try {
      $order = $event->order;
      $client = new \MongoDB\Client('mongodb://mongo:27017');
      $collection = $client->hyperf_events->order_events;
      $collection->insertOne([
        'event'      => 'order.created',
        'order_id'   => $order->id,
        'product'    => $order->product,
        'price'      => $order->price,
        'status'     => $order->status,
        'created_at' => new \MongoDB\BSON\UTCDateTime(),
      ]);
    } catch (\Throwable $e) {
    }
  }
}
