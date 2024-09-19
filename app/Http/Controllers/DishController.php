<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Dish;
use Illuminate\Support\Facades\Auth;

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

        $dishes = $query->paginate(5)->withQueryString();

        // Générer les liens de pagination avec les paramètres de filtre
        //$dishes->appends($request->all())->links();

        return view('welcome', compact('dishes', 'sortField', 'sortOrder'));
    }

    public function show(Request $request, string $slug) {
        $dish = Dish::where('slug', $slug)->firstOrFail();
        return view('dishes.show', compact("dish"));
    }

    public function edit(Request $request, string $slug)
    {
        if (!Auth::user()->hasPermissionTo('edit dishes')) {
            return redirect()->back()->with('danger', 'Vous n\'avez pas l\'autorisation de modifier un plat');
        }

        $dish = Dish::all()->where('slug', $slug)->firstOrFail();

        if (!Auth::user()->dishes()->where('slug', $slug)->exists()) {
            return redirect()->back()->with('danger', 'Vous ne pouvez pas modifier un plat que vous n\'avez pas crée');
        }
        $users = User::all();

        return view('dishes.edit', compact("dish", "users"));
    }

    public function update(Request $request, string $slug) {

        $dish = Dish::where('slug', $slug)->firstOrFail();
        $dish->update($this->validateDishData($request, $dish));

        return redirect()->route('dishes.index')->with('success', 'Dish mis à jour avec succès');
    }

    public function create(Request $request) {
        if (!Auth::user()->hasPermissionTo('create dishes')) {
            return redirect()->back()->with('danger', 'Vous n\'avez pas l\'autorisation de crée un plat');
        }
        $users = User::all(); // Récupérer tous les utilisateurs pour le champ d'auteur
        return view('dishes.create', compact('users'));
    }

    public function store(Request $request) {
        $data = $this->validatedDishData($request);
        Dish::create($data);
        return redirect()->route('dishes.index')->with('success', 'Dish créé avec succès');
    }


    public function delete(string $slug) {
        if (!Auth::user()->hasPermissionTo('delete dishes')) {
            return redirect()->back()->with('danger', 'Vous n\'avez pas l\'autorisation de supprimer un plat');
        }

        $dish = Dish::where('slug', $slug)->firstOrFail();
        $dish->delete();
        return redirect()->back()->with('success', 'Le dishes a bien été supprimé');
    }

    // Création de la fonction validateDishData() afin de ne plus repeter le même code au sein des mes fonctions store() et update()
    private function validateDishData(Request $request, ?Dish $dish = null): array {
        $rules = [
            'name' => ['required', 'max:255',  $dish ? "unique:dishes,name,{$dish->id}" : 'unique:dishes'],
            "description" => "required|max:2048",
            'image' => 'required|url',
            'user_id' => 'required|exists:users,id',
        ];

        return $request->validate($rules);
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
