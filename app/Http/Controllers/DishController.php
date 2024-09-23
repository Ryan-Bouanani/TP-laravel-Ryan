<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Dish;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class DishController extends Controller
{
    public function index(Request $request) {
        $query = Dish::query();

        // Apply filters based on search terms
        $this->applySearchFilters($query, $request);

        // Apply sorting based on request parameters
        $filtersSortingArray = $this->applySorting($query, $request);
        $sortField = $filtersSortingArray['sortField'];
        $sortOrder = $filtersSortingArray['sortOrder'];

        // Générer les liens de pagination avec les paramètres de filtre
        $dishes = $query->paginate(5)->withQueryString();

        return view('welcome', compact('dishes', 'sortField', 'sortOrder'));
    }

    public function show(Dish $dish) {
        return view('dishes.show', compact("dish"));
    }

    public function edit(Dish $dish)
    {
        $users = User::all();

        return view('dishes.edit', compact("dish", "users"));
    }

    public function update(UpdateDishRequest $request, Dish $dish) {

        $dish->update($request->validated());

        return redirect()->route('dishes.index')->with('success', 'Plat mis à jour avec succès');
    }

    public function create() {

        $users = User::all(); // Récupérer tous les utilisateurs pour le champ créateur
        return view('dishes.create', compact('users'));
    }

    public function store(StoreDishRequest $request) {

        Dish::create(array_merge($request->validated(), ['user_id' => Auth::id()]));

        return redirect()->route('dishes.index')->with('success', 'Plat créé avec succès');
    }

    public function destroy(Dish $dish) {

        // Si l'utilisateur n'est pas admin, il ne peut supprimer que ses propres plats
        if (!Auth::user()->hasRole('admin') && $dish->user->id !== Auth::id()) {
            return redirect()->back()->with('danger', 'Vous ne pouvez pas supprimer un plat que vous n\'avez pas créé');
        }

        $dish->delete();
        return redirect()->back()->with('success', 'Le plat a bien été supprimé');
    }

    private function applySearchFilters($query, $request) {
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('creator')) {
            $query->whereHas('user', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('creator') . '%');
            });
        }
        if ($request->filled('min_likes')) {
            $minLikes = $request->input('min_likes');
            $query->has('favoriteByUsers',  '>=', $minLikes);
        }

        // Handle min_likes filter (logic remains the same)
        if ($request->input('sort') === 'likes') {

           $query->withCount('favoriteByUsers')->orderBy('favorite_By_Users_count', $request->input('order'));

        }


    }

    private function applySorting($query, $request) {
        $sortField = $request->input('sort', 'id');
        $sortOrder = $request->input('order', 'asc');

        if ($sortField !== 'likes') {
            $query->orderBy($sortField, $sortOrder);
        }
        return [
            'sortField' => $sortField,
            'sortOrder'=> $sortOrder
        ];
    }
}
