<?php

namespace App\Observers;

use App\Jobs\SendDishCreatedNotification;
use App\Models\Dish;
use Illuminate\Support\Facades\DB;

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
     * Handle the Dish "deleting" event.
     */
    public function deleting(Dish $dish): void
    {
        // Supprimer toutes les entrées dans la table pivot favorites liées à ce plat
        DB::table('favorites')->where('dish_id', $dish->id)
            ->delete();
    }

    /**
     * Handle the Dish "deleted" event.
     */
    public function deleted(Dish $dish): void
    {

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
