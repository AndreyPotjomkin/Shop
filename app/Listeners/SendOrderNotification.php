<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle(OrderCreated $event): void
    {
        Log::info('Order created', [
            'order_id' => $event->order->id,
            'user_id' => $event->order->user_id,
            'total' => $event->order->total,
        ]);
    }
} 