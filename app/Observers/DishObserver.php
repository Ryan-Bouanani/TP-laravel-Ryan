<?php

namespace App\Observers;

use App\Mail\PublishedDish;
use App\Models\Dish;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class DishObserver
{
    /**
     * Handle the Dish "created" event.
     */
    public function created(Dish $dish): void
    {
        // $user = Auth::user()?: $dish->user()->first();

        // Si utilisateur connecté on envoie notif
        if (Auth::check()) {
            $user = Auth::user();
            Mail::to($user->email)->send(new PublishedDish($dish, $user));
            $user->notify(new \App\Notifications\PublishedDish($dish, $user));
        }
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
