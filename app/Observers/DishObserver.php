<?php

namespace App\Observers;

use App\Jobs\SendDishCreatedNotification;
use App\Models\Dish;

class DishObserver
{
    /**
     * Handle the Dish "created" event.
     */
    public function created(Dish $dish): void
    {
        SendDishCreatedNotification::dispatchSync($dish);
    }

    /**
     * Handle the Dish "updated" event.
     */
    public function updated(Dish $dish): void
    {
        //
    }

    /**
     * Handle the Dish "deleted" event.
     */
    public function deleted(Dish $dish): void
    {
        //
    }

    /**
     * Handle the Dish "restored" event.
     */
    public function restored(Dish $dish): void
    {
        //
    }

    /**
     * Handle the Dish "force deleted" event.
     */
    public function forceDeleted(Dish $dish): void
    {
        //
    }
}
