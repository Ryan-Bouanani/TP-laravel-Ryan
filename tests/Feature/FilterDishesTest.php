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
     * Tests the filter by name functionality
     */
    public function test_filter_order_desc_by_id()
    {
        $user = User::factory()->create();
        $dish1 = Dish::factory()->create();
        $dish2 = Dish::factory()->create();
        $dish3 = Dish::factory()->create();

        // Simuler une recherche par créateur
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['sort' => 'id', 'order' => 'desc']));;

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas('dishes');

        // Verify the sorting order
        $dishes = $response->original['dishes'];
        $this->assertEquals($dish3->id, $dishes->first()->id);
        $this->assertEquals($dish1->id, $dishes->last()->id);

    }

    public function test_filter_order_asc_by_id()
    {
        $user = User::factory()->create();
        $dish1 = Dish::factory()->create();
        $dish2 = Dish::factory()->create();
        $dish3 = Dish::factory()->create();

        // Simuler une recherche par créateur
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['sort' => 'id', 'order' => 'asc']));;

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas('dishes');

        // Verify the sorting order
        $dishes = $response->original['dishes'];
        $this->assertEquals($dish1->id, $dishes->first()->id);
        $this->assertEquals($dish3->id, $dishes->last()->id);

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
        $response->assertViewIs('welcome');
        $response->assertViewHas('dishes');

        // Vérifier que seul le plat 1 est retourné
        $response->assertSee($dish1->name);
        $response->assertDontSee($dish2->name);
    }

    public function test_filter_order_desc_by_name()
    {
        $user = User::factory()->create();
        $dish1 = Dish::factory()->create(['name' => 'a']);
        $dish2 = Dish::factory()->create(['name' => 'b']);
        $dish3 = Dish::factory()->create(['name' => 'c']);

        // Simuler une recherche par créateur
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['sort' => 'name', 'order' => 'desc']));;

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas('dishes');

        // Verify the sorting order
        $dishes = $response->original['dishes'];
        $this->assertEquals($dish3->id, $dishes->first()->id);
        $this->assertEquals($dish1->id, $dishes->last()->id);

    }

    public function test_filter_order_asc_by_name()
    {
        $user = User::factory()->create();
        $dish1 = Dish::factory()->create(['name' => 'a']);
        $dish2 = Dish::factory()->create(['name' => 'b']);
        $dish3 = Dish::factory()->create(['name' => 'c']);

        // Simuler une recherche par créateur
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['sort' => 'name', 'order' => 'asc']));;

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas('dishes');

        // Verify the sorting order
        $dishes = $response->original['dishes'];
        $this->assertEquals($dish1->id, $dishes->first()->id);
        $this->assertEquals($dish3->id, $dishes->last()->id);

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
        $user1 = User::factory()->create(['name' => 'a']);
        $user2 = User::factory()->create(['name' => 'b']);
        $user3 = User::factory()->create(['name' => 'c']);

        $dish1 = Dish::factory()->create();
        $dish2 = Dish::factory()->create();
        $dish3 = Dish::factory()->create();

        $dish1->favoriteByUsers()->attach($user1);
        $dish2->favoriteByUsers()->attach($user2);
        $dish3->favoriteByUsers()->attach($user3);

        // Simuler une recherche par créateur
        $response = $this->actingAs($user1)
            ->get(route('dishes.index', ['sort' => 'user_id', 'order' => 'desc']));;

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas('dishes');

        // Verify the sorting order
        $dishes = $response->original['dishes'];
        $this->assertEquals($dish3->id, $dishes->first()->id);
        $this->assertEquals($dish1->id, $dishes->last()->id);

    }

    public function test_filter_order_asc_by_creator()
    {
        $user1 = User::factory()->create(['name' => 'a']);
        $user2 = User::factory()->create(['name' => 'b']);
        $user3 = User::factory()->create(['name' => 'c']);

        $dish1 = Dish::factory()->create();
        $dish2 = Dish::factory()->create();
        $dish3 = Dish::factory()->create();

        $dish1->favoriteByUsers()->attach($user1);
        $dish2->favoriteByUsers()->attach($user2);
        $dish3->favoriteByUsers()->attach($user3);

        // Simuler une recherche par créateur
        $response = $this->actingAs($user1)
            ->get(route('dishes.index', ['sort' => 'user_id', 'order' => 'asc']));;

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas('dishes');

        // Verify the sorting order
        $dishes = $response->original['dishes'];
        $this->assertEquals($dish1->id, $dishes->first()->id);
        $this->assertEquals($dish3->id, $dishes->last()->id);

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
        $response->assertViewIs('welcome');
        $response->assertViewHas('dishes');

        // Vérifier que seul le plat avec 5 likes est retourné
        $response->assertSee($dishWith5Favorites->name);
        $response->assertDontSee($dishWith3Favorites->name);
    }

    public function test_filter_order_desc_by_min_likes()
    {
        // Create dishes with different numbers of likes and assign them to a user
        $user = User::factory()->create();
        $dish1 = Dish::factory()->create();
        $dish2 = Dish::factory()->create();
        $dish3 = Dish::factory()->create();

        $dish3->favoriteByUsers()->attach(User::factory()->count(5)->create());
        $dish2->favoriteByUsers()->attach(User::factory()->count(4)->create());
        $dish1->favoriteByUsers()->attach(User::factory()->count(3)->create());

        // Simulate a search by minimum likes and sort order
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['sort' => 'likes', 'order' => 'desc']));

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas('dishes');

        // Verify the sorting order
        $dishes = $response->original['dishes'];
        $this->assertEquals($dish3->id, $dishes->first()->id);
        $this->assertEquals($dish1->id, $dishes->last()->id);
    }

    public function test_filter_order_asc_by_min_likes()
    {
        // Create dishes with different numbers of likes and assign them to a user
        $user = User::factory()->create();
        $dish1 = Dish::factory()->create(['user_id' => $user->id]);
        $dish2 = Dish::factory()->create(['user_id' => $user->id]);
        $dish3 = Dish::factory()->create(['user_id' => $user->id]);

        $dish3->favoriteByUsers()->attach(User::factory()->count(5)->create());
        $dish2->favoriteByUsers()->attach(User::factory()->count(4)->create());
        $dish1->favoriteByUsers()->attach(User::factory()->count(3)->create());

        // Simulate a search by minimum likes and sort order
        $response = $this->actingAs($user)
            ->get(route('dishes.index', ['sort' => 'likes', 'order' => 'asc']));

        // Verify the response
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas('dishes');

        // Verify the sorting order
        $dishes = $response->original['dishes'];

        // Vérifier que les plats sont bien triés par ID ascendant
        $this->assertEquals($dish1->id, $dishes->first()->id); // Assuming dish1 has a lower ID
        $this->assertEquals($dish3->id, $dishes->last()->id);
    }
}
