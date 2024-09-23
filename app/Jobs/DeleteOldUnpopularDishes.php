<?php

namespace App\Jobs;

use App\Models\Dish;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteOldUnpopularDishes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $deletedCount = Dish::where("created_at" ,'<', now()->submonths())
            ->has('favoriteByUsers',  '<', 5)
            ->delete();
        Log::info("Nombre de plats supprimés (job) : {$deletedCount}");
    }
}
