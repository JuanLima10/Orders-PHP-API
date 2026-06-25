<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Controller\OrderController;
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::get('/orders', [OrderController::class, 'index']);
Router::get('/orders/{id}', [OrderController::class, 'show']);
Router::post('/orders', [OrderController::class, 'store']);
Router::put('/orders/{id}', [OrderController::class, 'update']);
Router::delete('/orders/{id}', [OrderController::class, 'delete']);
