<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FilterDishesTest extends TestCase
{
    use refreshDatabase;

    /**
     * Tests the filter by id functionality
     */
    public function test_filter_order_desc_by_id()
    {
        $user = User::factory()->create();
        $dishes = Dish::factory()->count(5)->create();

        // Simuler une recherche par créateur
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['sort' => 'id', 'order' => 'desc']));;

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertViewHas('dishes');

        // Récupérer les plats de la vue
        $returnedDishes = $response->viewData('dishes');

        // Créer une collection triée manuellement pour la comparaison
        $sortedDishes = $dishes->sortByDesc('id')->values();

        // Vérifier que l'ordre des plats retournés correspond à l'ordre trié manuellement
        $this->assertEquals(
            $sortedDishes->pluck('id')->toArray(),
            $returnedDishes->pluck('id')->toArray()
        );
    }

    public function test_filter_order_asc_by_id()
    {
        $user = User::factory()->create();
        $dishes = Dish::factory()->count(5)->create();

        // Simuler une recherche par créateur
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['sort' => 'id', 'order' => 'asc']));;

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertViewHas('dishes');

        // Récupérer les plats de la vue
        $returnedDishes = $response->viewData('dishes');

        // Créer une collection triée manuellement pour la comparaison
        $sortedDishes = $dishes->sortBy('id')->values();

        // Vérifier que l'ordre des plats retournés correspond à l'ordre trié manuellement
        $this->assertEquals(
            $sortedDishes->pluck('id')->toArray(),
            $returnedDishes->pluck('id')->toArray()
        );
    }



    /**
     * Tests the filter by name functionality
     */
    public function test_filter_by_name()
    {
        $user = User::factory()->create();
        // Créer des plats avec différents noms
        $dish1 = Dish::factory()->create(['name' => 'Plat 1']);
        $dish2 = Dish::factory()->create(['name' => 'Plat 2']);

        // Simuler une recherche par nom
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['name' => 'Plat 1']));

        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertViewHas('dishes');

        // Vérifier que seul le plat 1 est retourné
        $response->assertSee($dish1->name);
        $response->assertDontSee($dish2->name);
    }

    public function test_filter_order_desc_by_name()
    {
        $user = User::factory()->create();
        $dishes = Dish::factory()->createMany([
            ['name' => 'a'],
            ['name' => 'b'],
            ['name' => 'c'],
            ['name' => 'd'],
            ['name' => 'e'],
        ]);

        // Simuler une recherche par créateur
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['sort' => 'name', 'order' => 'desc']));;

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertViewHas('dishes');

        // Récupérer les plats de la vue
        $returnedDishes = $response->viewData('dishes');

        // Créer une collection triée manuellement pour la comparaison
        $sortedDishes = $dishes->sortByDesc('name')->values();

        // Vérifier que l'ordre des plats retournés correspond à l'ordre trié manuellement
        $this->assertEquals(
            $sortedDishes->pluck('name')->toArray(),
            $returnedDishes->pluck('name')->toArray()
        );
    }

    public function test_filter_order_asc_by_name()
    {
        $user = User::factory()->create();
        $dishes = Dish::factory()->createMany([
            ['name' => 'a'],
            ['name' => 'b'],
            ['name' => 'c'],
            ['name' => 'd'],
            ['name' => 'e'],
        ]);

        // Simuler une recherche par créateur
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['sort' => 'name', 'order' => 'asc']));;

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertViewHas('dishes');

        // Récupérer les plats de la vue
        $returnedDishes = $response->viewData('dishes');

        // Créer une collection triée manuellement pour la comparaison
        $sortedDishes = $dishes->sortBy('name')->values();

        // Vérifier que l'ordre des plats retournés correspond à l'ordre trié manuellement
        $this->assertEquals(
            $sortedDishes->pluck('name')->toArray(),
            $returnedDishes->pluck('name')->toArray()
        );
    }



    /**
     * Tests the filter by creator functionality
     */
    public function test_filter_by_creator()
    {
        $user = User::factory()->create();

        // Créer des plats avec différents créateurs
        $dish1 = Dish::factory()->create();
        $dish2 = Dish::factory()->create();

        // Simuler une recherche par créateur
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['creator' => $dish1->user->name]));;

        // Vérifier que seul le plat 1 est retourné
        $response->assertSee($dish1->name);
        $response->assertDontSee($dish2->name);
    }

    public function test_filter_order_desc_by_creator()
    {
        $users = User::factory()->createMany([
            ['name' => 'a'],
            ['name' => 'b'],
            ['name' => 'c'],
            ['name' => 'd'],
            ['name' => 'e'],
        ]);

        $dishes = Dish::factory()->createMany([
            ['user_id' => $users[0]],
            ['user_id' => $users[1]],
            ['user_id' => $users[2]],
            ['user_id' => $users[3]],
            ['user_id' => $users[4]],
        ]);

        // Simuler une recherche par créateur
        $response = $this->actingAs($users[0])
            ->get(route('dishes.index', ['sort' => 'user_id', 'order' => 'desc']));;

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertViewHas('dishes');

        // Récupérer les plats de la vue
        $returnedDishes = $response->viewData('dishes');

        // Créer une collection triée manuellement pour la comparaison
        $sortedDishes = $dishes->sortByDesc('user.name')->values();

        // Vérifier que l'ordre des plats retournés correspond à l'ordre trié manuellement
        $this->assertEquals(
            $sortedDishes->pluck('user.name')->toArray(),
            $returnedDishes->pluck('user.name')->toArray()
        );

    }

    public function test_filter_order_asc_by_creator()
    {
        $users = User::factory()->createMany([
            ['name' => 'a'],
            ['name' => 'b'],
            ['name' => 'c'],
            ['name' => 'd'],
            ['name' => 'e'],
        ]);

        $dishes = Dish::factory()->createMany([
            ['user_id' => $users[0]],
            ['user_id' => $users[1]],
            ['user_id' => $users[2]],
            ['user_id' => $users[3]],
            ['user_id' => $users[4]],
        ]);

        // Simuler une recherche par créateur
        $response = $this->actingAs($users[0])
            ->get(route('dishes.index', ['sort' => 'user_id', 'order' => 'asc']));;

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertViewHas('dishes');

        // Récupérer les plats de la vue
        $returnedDishes = $response->viewData('dishes');

        // Créer une collection triée manuellement pour la comparaison
        $sortedDishes = $dishes->sortBy('user.name')->values();

        // Vérifier que l'ordre des noms des créateurs des plats retournés correspond à l'ordre trié manuellement
        $this->assertEquals(
            $sortedDishes->pluck('user.name')->toArray(),
            $returnedDishes->pluck('user.name')->toArray()
        );
    }



    /**
     * Tests the filter by min likes functionality
     */
    public function test_filter_by_min_likes()
    {
        // Créer des plats avec un nombre de favoris différent
        $dishWith3Favorites = Dish::factory()->create();
        $dishWith3Favorites->favoriteByUsers()->attach(User::factory()->count(3)->create());

        $dishWith5Favorites = Dish::factory()->create();
        $dishWith5Favorites->favoriteByUsers()->attach(User::factory()->count(5)->create());

        // Simuler une recherche par nombre minimum de likes
        $response = $this->actingAs(User::factory()->create())
            ->get(route('dishes.index', ['min_likes' => 4]));

        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertViewHas('dishes');

        // Vérifier que seul le plat avec 5 likes est retourné
        $response->assertSee($dishWith5Favorites->name);
        $response->assertDontSee($dishWith3Favorites->name);
    }

    public function test_filter_order_desc_by_min_likes()
    {
        // Create dishes with different numbers of likes and assign them to a user
        $user = User::factory()->create();
        $dishes = Dish::factory(3)->for($user, 'user')->createMany();


        $dishes[2]->favoriteByUsers()->attach(User::factory()->count(5)->create());
        $dishes[1]->favoriteByUsers()->attach(User::factory()->count(4)->create());
        $dishes[0]->favoriteByUsers()->attach(User::factory()->count(3)->create());

        // Simulate a search by minimum likes and sort order
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['sort' => 'likes', 'order' => 'desc']));

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertViewHas('dishes');

        // Récupérer les plats de la vue
        $returnedDishes = $response->viewData('dishes');

        // Créer une collection triée manuellement pour la comparaison
        // $sortedDishes = $dishes->sortByDesc('favoriteByUsers')->values();
        $sortedDishes = $dishes->sortByDesc(function ($dish) {
            return $dish->favoriteByUsers()->count();
        })->values();

        // Vérifier que l'ordre des plats retournés correspond à l'ordre trié manuellement
        $this->assertEquals(
            $sortedDishes->pluck('id')->toArray(),
            $returnedDishes->pluck('id')->toArray()
        );
    }

    public function test_filter_order_asc_by_min_likes()
    {
        // Create dishes with different numbers of likes and assign them to a user
        $user = User::factory()->create();
        $dishes = Dish::factory(3)->for($user, 'user')->createMany();


        $dishes[2]->favoriteByUsers()->attach(User::factory()->count(5)->create());
        $dishes[1]->favoriteByUsers()->attach(User::factory()->count(4)->create());
        $dishes[0]->favoriteByUsers()->attach(User::factory()->count(3)->create());

        // Simulate a search by minimum likes and sort order
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['sort' => 'likes', 'order' => 'asc']));

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertViewHas('dishes');

        // Récupérer les plats de la vue
        $returnedDishes = $response->viewData('dishes');

        // Créer une collection triée manuellement pour la comparaison
        $sortedDishes = $dishes->sortBy(function ($dish) {
            return $dish->favoriteByUsers()->count();
        })->values();

        // Vérifier que l'ordre des plats retournés correspond à l'ordre trié manuellement
        $this->assertEquals(
            $sortedDishes->pluck('id')->toArray(),
            $returnedDishes->pluck('id')->toArray()
        );
    }
}
