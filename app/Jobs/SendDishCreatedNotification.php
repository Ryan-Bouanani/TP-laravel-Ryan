<?php

namespace App\Jobs;

use App\Mail\PublishedDish;
use App\Models\Dish;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendDishCreatedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Dish $dish,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->dish->user;
        // Mail::to($user->email)->send(new PublishedDish($this->dish, $user));
        $user->notify(new \App\Notifications\PublishedDish($this->dish, $user));
    }
}
