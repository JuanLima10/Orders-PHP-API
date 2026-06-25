<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $product
 * @property float $price
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Order extends Model
{
    protected ?string $table = 'orders';

    protected array $fillable = [
        'product',
        'price',
        'status',
    ];

    protected array $casts = [
        'id'         => 'integer',
        'price'      => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
