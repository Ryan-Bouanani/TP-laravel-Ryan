<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddToFavoriteDishTest extends TestCase
{
    use refreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_add_and_remove_dish_from_favorites()
    {
        // Créer un utilisateur et un plat
        $dish = Dish::factory()->create();
        $user = $dish->user;

        // Test d'ajout aux favoris
        $response = $this->actingAs($user)
            ->post(route('toggleFavoriteDish', $user->id), [
                'dish_id' => $dish->id
            ]);

        // Vérifier la redirection
        $response->assertRedirect(route('dishes.index'));
        $response->assertSessionHas('success', 'Plat ajouté aux favoris !');

        // Vérifier que le plat a été ajouté aux favoris
        $this->assertTrue($user->favoriteDishes()->where('dish_id', $dish->id)->exists());

        // Test de retrait des favoris (en réexécutant la même action)
        $response = $this->actingAs($user)
            ->post(route('toggleFavoriteDish', $user->id), [
                'dish_id' => $dish->id
            ]);

        // Vérifier la redirection
        $response->assertRedirect(route('dishes.index'));
        $response->assertSessionHas('success', 'Plat retiré des favoris !');


        // Vérifier que le plat a été retiré des favoris
        $this->assertFalse($user->favoriteDishes()->where('dish_id', $dish->id)->exists());
    }
}
