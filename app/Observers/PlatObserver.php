<?php

namespace App\Observers;

use App\Mail\PublishedDish;
use App\Models\Plat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PlatObserver
{
    /**
     * Handle the Plat "created" event.
     */
    public function created(Plat $plat): void
    {
        // $user = Auth::user()?: $plat->user()->first();

        // Si utilisateur connecté on envoie notif
        if (Auth::check()) {
            $user = Auth::user();
            $user = User::all()->find($user->id);
            Mail::to($user->email)->send(new PublishedDish($plat, $user));
            $user->notify(new \App\Notifications\PublishedDish($plat, $user));
        }
    }

    /**
     * Handle the Plat "updated" event.
     */
    public function updated(Plat $plat): void
    {
        //
    }

    /**
     * Handle the Plat "deleted" event.
     */
    public function deleted(Plat $plat): void
    {
        //
    }

    /**
     * Handle the Plat "restored" event.
     */
    public function restored(Plat $plat): void
    {
        //
    }

    /**
     * Handle the Plat "force deleted" event.
     */
    public function forceDeleted(Plat $plat): void
    {
        //
    }
}
