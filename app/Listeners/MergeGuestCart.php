<?php

namespace App\Listeners;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;

class MergeGuestCart
{
    /**
     * Create the event listener.
     */
    public function __construct(private CartService $cart)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $this->cart->mergeGuestCart($event->user);
    }
}
