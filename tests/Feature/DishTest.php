<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DishTest extends TestCase
{
    // use refreshDatabase, WithFaker;
    use refreshDatabase;

    /**
     * Tests a listing of the dishes
     */
    public function test_get_dishes(): void
    {
        $user = User::factory()->create();
        // Créer des plats avec factory
        Dish::factory()->count(3)->create();

        $response = $this->actingAs($user)->get('/dishes');

        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertViewHas('dishes');
    }



    /**
     * Tests the dish editing functionality
     */
    public function test_edit_dishes_as_admin(): void
    {
        // Créer un utilisateur admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Créer un plat
        $dish = Dish::factory()->create();

        // Authentifier l'utilisateur
        $response = $this->actingAs($admin)->get("/dishes/{$dish->slug}/edit");

        // Vérifier que la réponse est correcte
        $response->assertStatus(200);
        $response->assertViewIs('dishes.edit');
        $response->assertViewHas('dish', $dish);
        $response->assertViewHas('users');
    }

    public function test_edit_dishes_as_non_admin(): void {
        // Tester avec un utilisateur non-admin
        $user = User::factory()->create();

        // Créer un plat
        $dish = Dish::factory()->for($user, 'user')->create();

        $response = $this
            ->actingAs($user)
            ->get("/dishes/{$dish->slug}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('dishes.edit');
        $response->assertViewHas('dish', $dish);
        $response->assertViewMissing('users');
    }



    /**
     * Tests the dish updating functionality
     */
    public function test_update_dishes(): void {

        // Créer un utilisateur
        $user = User::factory()->create();

        // Créer un plat
        $dish = Dish::factory()->for($user, 'user')->create();



        $updatedData = [
            'id' => $dish->id,
            'name' => 'Nouveau nom de plat',
            'description' => 'Nouvelle description du plat',
            'image' => 'https://example.com/image.jpg',
            'user_id' => $user->id,
        ];


        $response = $this
            ->actingAs($user)
            ->patch("/dishes/{$dish->slug}", $updatedData);

        // dd(DB::table('dishes')->where('id', $dish->id)->first(), $updatedData);

        // Vérifier la redirection
        $response->assertRedirect(route('dishes.index'));
        $response->assertSessionHas('success', 'Plat mis à jour avec succès');

        $dish->refresh();

        $this->assertDatabaseHas('dishes', [
            'name' => $updatedData['name'],
            //'description' => Crypt::decryptString($dish->refresh->description),
            'image' => $updatedData['image'],
            'user_id' => $updatedData['user_id'],
        ]);
    }



    /**
     * Tests the dish create functionality
     */
    public function test_create_dishes_as_admin(): void
    {
        // Créer un nombre spécifique d'utilisateurs pour que chaque utilisateur est présent dans la $response
        $numberOfUsers = 3;
        $users = User::factory()->count($numberOfUsers)->create();

        // Créer un utilisateur admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Simuler une requête GET authentifiée vers la route de création
        $response = $this
            ->actingAs($admin)
            ->get(route('dishes.create'));

        // Vérifier que la réponse a un statut 200 (OK)
        $response->assertStatus(200);

        // Vérifier que la vue correcte est retournée
        $response->assertViewIs('dishes.create');

        // Vérifier que la variable 'users' est passée à la vue
        $response->assertViewHas('users');

        // Vérifier que tous les utilisateurs sont présents dans la variable 'users'
        $responseUsers = $response->viewData('users');

        $this->assertCount($numberOfUsers + 1, $responseUsers); // +1 pour l'admin

        // Vérifier que l'admin est inclus dans la liste des utilisateurs
        $this->assertTrue($responseUsers->contains($admin));

        // Vérifier que chaque utilisateur créé est présent dans la liste
        foreach ($users as $user) {
            $this->assertTrue($responseUsers->contains($user));
        }
    }

    public function test_create_dish_as_non_admin(): void
    {
        // Créer un utilisateur non-admin
        $user = User::factory()->create();

        // Simuler une requête GET authentifiée à la route de création
        $response = $this
            ->actingAs($user)
            ->get(route('dishes.create'));

        // Vérifier que la réponse est correcte
        $response->assertStatus(200);
        $response->assertViewIs('dishes.create');
        $response->assertViewMissing('users');
    }



    /**
     * Tests the dish store functionality
     */
    public function test_store_dish_with_valid_data(): void
    {
        $user = User::factory()->create();

        $dishData = [
            'name' => 'Nouveau Plat',
            'description' => 'Description du nouveau plat',
            'image' => 'https://example.com/image.jpg',
            'user_id' => $user->id,
        ];

        $response = $this
            ->actingAs($user)
            ->post(route('dishes.store'), $dishData);

        $response->assertRedirect(route('dishes.index'));
        $response->assertSessionHas('success', 'Plat créé avec succès');

        // Vérifier que le plat a été créé avec les bonnes données
        $this->assertDatabaseHas('dishes', [
            'name' => $dishData['name'],
            'image' => $dishData['image'],
            'user_id' => $dishData['user_id'],
        ]);

        // Vérifier que la description a été correctement cryptée et stockée
        $dish = Dish::where('name', $dishData['name'])->first();
        $this->assertNotNull($dish);
        $this->assertEquals($dishData['description'], $dish->description);
    }


    public function test_store_dish_with_invalid_data(): void
    {
        $user = User::factory()->create();

        $invalidDishData = [
            'name' => '', // Nom vide, devrait échouer la validation
            'description' => '', // Description vide, devrait échouer la validation
            'image' => 'not-a-url', // URL invalide, devrait échouer la validation
            'user_id' => 9999, // ID utilisateur inexistant
        ];

        $response = $this
            ->actingAs($user)
            ->post(route('dishes.store'), $invalidDishData);

        $response->assertSessionHasErrors(['name', 'description', 'image', 'user_id']);

        $this->assertDatabaseMissing('dishes', ['name' => $invalidDishData['name']]);
    }

    public function test_store_with_duplicate_name(): void  {

        $user = User::factory()->create();

        // Créer un plat existant avec le même utilisateur
        $existingDish = Dish::factory()->for($user,'user')->create([
            'name' => 'Plat Existant',
        ]);

        $dishData = [
            'name' => 'Plat Existant', // Même nom que le plat existant
            'description' => 'Nouvelle description',
            'image' => 'https://example.com/image.jpg',
            'user_id' => $user->id,
        ];

        $response = $this->actingAs($user)
            ->post(route('dishes.store'), $dishData);

        $response->assertSessionHasErrors('name');

        // Vérifier qu'un seul plat existe
        $this->assertDatabaseCount('dishes', 1);
    }



    /**
     * Tests the dish destroy functionality
     */
    public function test_destroy_dishes_as_admin(): void {

        // Admins can delete any dish they wish, even if they are not the dish's creator.
        $admin = User::factory()->create();
        $admin = $admin->assignRole('admin');

        $dish = Dish::factory()->create();


        $response = $this->actingAs($admin)
            ->delete(route('dishes.destroy', $dish->slug));

        $response->assertRedirect(route('dishes.index'));
        $response->assertSessionHas('success', 'Le plat a bien été supprimé');

        // Verify dish is deleted from database
        $this->assertModelMissing($dish);
    }

    public function test_destroy_dishes_as_user(): void {

        $dish = Dish::factory()->create();

        // Créer un utilisateur
        $user = $dish->user;

        $response = $this->actingAs($user)
            ->delete(route('dishes.destroy', $dish->slug));

        $response->assertRedirect(route('dishes.index'));
        $response->assertSessionHas('success', 'Le plat a bien été supprimé');

        // Verify dish is deleted from database
        $this->assertModelMissing($dish);
    }

    public function test_destroy_dish_with_unauthorized_user(): void
    {
        // Create a user (not admin)
        $user = User::factory()->create();

        // Create a dish with a different owner
        $dish = Dish::factory()->create();

        // Authenticate as the unauthorized user
        $response = $this->actingAs($user)->delete(route('dishes.destroy', $dish->slug));

        // Assert unauthorized access
        $response->assertStatus(302);
        $response->assertRedirect(route('dishes.index'));
        $response->assertSessionHas('danger', 'Vous ne pouvez pas supprimer un plat que vous n\'avez pas créé');

        // Verify dish is still present
        $this->assertDatabaseHas('dishes', [
            'id' => $dish->id,
        ]);
    }



    /**
     * Tests the dish detail display functionality
     */
    public function test_show_dish(): void {
        $user = User::factory()->create();
        $dish = Dish::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dishes.show', $dish->slug));

        // Vérifier que la réponse est correcte
        $response->assertStatus(200);
        $response->assertViewIs('dishes.show');
        $response->assertViewHas('dish', $dish);

        // Optionally, verify the content of the view (if applicable)
        $response->assertSeeText($dish->name);
        $response->assertSee($dish->image);
        $response->assertSeeText($dish->description);
        $response->assertSeeText($dish->user->name);
    }
}
