<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Dish;
use Illuminate\Support\Facades\Auth;

class DishController extends Controller
{
    /**
     * Display a listing of the dishes.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        $query = Dish::with('user');

        // Apply filters based on search terms
        $this->filterDishes($query, $request);

        // Apply sorting based on request parameters
        $filtersSortingArray = $this->sortDishes($query, $request);
        $sortField = $filtersSortingArray['sortField'];
        $sortOrder = $filtersSortingArray['sortOrder'];

        // Générer les liens de pagination avec les paramètres de filtre
        $dishes = $query->paginate(5)->withQueryString();

        return view('index', compact('dishes', 'sortField', 'sortOrder'));
    }


    /**
     * Display the specified dish.
     *
     * @param Dish $dish
     * @return \Illuminate\View\View
     */
    public function show(Dish $dish) {
        return view('dishes.show', compact("dish"));
    }


    /**
     * Show the form for editing the specified dish.
     *
     * @param Dish $dish
     * @return \Illuminate\View\View
     */
    public function edit(Dish $dish)
    {
        if(Auth::user()->hasRole('admin')) {
            $users = User::all();
            return view('dishes.edit', compact("dish", "users"));
        }

        return view('dishes.edit', compact("dish"));
    }


    /**
     * Update the specified dish in database.
     *
     * @param UpdateDishRequest $request
     * @param Dish $dish
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateDishRequest $request, Dish $dish) {

        $dish->update($request->validated());

        return redirect()->route('dishes.index')->with('success', 'Plat mis à jour avec succès');
    }


    /**
     * Show the form for creating a new dish.
     *
     * @return \Illuminate\View\View
     */
    public function create() {

        if(Auth::user()->hasRole('admin')) {
            $users = User::all();
            return view('dishes.create', compact("users"));
        }

        return view('dishes.create');
    }



    /**
     * Store a new created dish in database.
     *
     * @param StoreDishRequest $request The validated request containing dish data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreDishRequest $request) {

        Dish::create($request->validated());

        return redirect()->route('dishes.index')->with('success', 'Plat créé avec succès');
    }


    /**
     * Remove the specified dish from storage.
     *
     * @param Dish $dish The dish model to be deleted
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Dish $dish) {

        // Si l'utilisateur n'est pas admin, il ne peut supprimer que ses propres plats
        if (!Auth::user()->hasRole('admin') && $dish->user->id !== Auth::id()) {
            return redirect()->route('dishes.index')->with('danger', 'Vous ne pouvez pas supprimer un plat que vous n\'avez pas créé');
        }

        $dish->delete();
        return redirect()->route('dishes.index')->with('success', 'Le plat a bien été supprimé');
    }


    /**
     * Apply search filters to the dish query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     */
    private function filterDishes($query, $request) {

        // Filter by name if 'name' field is filled in the request
        $query->when($request->filled('name'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->input('name') . '%');
        });

        // Filter by creator if 'creator' field is filled in the request
        $query->when($request->filled('creator'), function ($q) use ($request) {
            $q->whereHas('user', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('creator') . '%');
            });
        });

        // Filter by minimum likes if 'min_likes' field is filled in the request
        $query->when($request->filled('min_likes'), function ($q) use ($request) {
            $minLikes = $request->input('min_likes');
            $q->has('favoriteByUsers', '>=', $minLikes);
        });
    }


    /**
     * Apply sorting to the dish query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @return array
     */
    private function sortDishes($query, $request) {

        $sortField = $request->input('sort', 'id');
        $sortOrder = $request->input('order', 'asc');

         match($sortField) {
            'likes'=>$query->withCount('favoriteByUsers')->orderBy('favorite_by_users_count', $sortOrder),
            'creator' => $query->join('users', 'users.id', '=', 'dishes.user_id')
            ->orderBy('users.name', $sortOrder),
            default => $query->orderBy($sortField, $sortOrder),
        };

        return [
            'sortField' => $sortField,
            'sortOrder'=> $sortOrder
        ];
    }
}
