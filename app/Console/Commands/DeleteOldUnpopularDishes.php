<?php

namespace App\Console\Commands;

use App\Models\Dish;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteOldUnpopularDishes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-unpopular-dishes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprimer les plats créés il y a plus d\'un mois avec moins de 5 likes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Delete dishes that have had less than 5 likes in 1 month (command "app:delete-old-unpopular-dishes")
        $deletedCount = Dish::where("created_at" ,'<', now()->submonths())
            ->has('favoriteByUsers',  '<', 5)
            ->delete();
        Log::info("Nombre de plats supprimés (command): {$deletedCount}");
    }
}
