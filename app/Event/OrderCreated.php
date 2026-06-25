<?php

declare(strict_types=1);

namespace App\Event;

use App\Model\Order;

class OrderCreated
{
  public function __construct(public readonly Order $order) {}
}
