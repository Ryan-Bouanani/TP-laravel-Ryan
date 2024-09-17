<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Plat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlatController extends Controller
{
    public function index() {
        $userId = Auth::id();
        $plats = Plat::with(['user', 'favoriteByUsers'])
            ->orderByRaw("CASE WHEN user_id = ? THEN 0 ELSE 1 END", [$userId])
            ->latest()
            ->paginate(5)
        ;

        return view('welcome', compact('plats'));
    }

    public function show(Request $request, string $slug) {
        $plat = Plat::where('slug', $slug)->firstOrFail();
        return view('plats.show', compact("plat"));
    }

    public function edit(Request $request, string $slug)
    {
        if (!Auth::user()->hasPermissionTo('edit plats')) {
            return redirect()->back()->with('danger', 'Vous n\'avez pas l\'autorisation de modifier un plat');
        }

        $plat = Plat::all()->where('slug', $slug)->firstOrFail();

        if (!Auth::user()->plats()->where('slug', $slug)->exists()) {
            return redirect()->back()->with('danger', 'Vous ne pouvez pas modifier un plat que vous n\'avez pas crée');
        }
        $users = User::all();

        return view('plats.edit', compact("plat", "users"));
    }

    public function update(Request $request, string $slug) {

        $plat = Plat::where('slug', $slug)->firstOrFail();
        $plat->update($this->validatePlatData($request, $plat));

        return redirect()->route('plats.index')->with('success', 'Plat mis à jour avec succès');
    }

    public function create(Request $request) {
        if (!Auth::user()->hasPermissionTo('create plats')) {
            return redirect()->back()->with('danger', 'Vous n\'avez pas l\'autorisation de crée un plat');
        }
        $users = User::all(); // Récupérer tous les utilisateurs pour le champ d'auteur
        return view('plats.create', compact('users'));
    }

    public function store(Request $request) {
        $data = $this->validatePlatData($request);
        Plat::create($data);
        return redirect()->route('plats.index')->with('success', 'Plat créé avec succès');
    }


    public function delete(string $slug) {
        if (!Auth::user()->hasPermissionTo('delete plats')) {
            return redirect()->back()->with('danger', 'Vous n\'avez pas l\'autorisation de supprimer un plat');
        }

        $plat = Plat::where('slug', $slug)->firstOrFail();
        $plat->delete();
        return redirect()->back()->with('success', 'Le plats a bien été supprimé');
    }

    // Création de la fonction validatePlatData() afin de ne plus repeter le même code au sein des mes fonctions store() et update()
    private function validatePlatData(Request $request, ?Plat $plat = null): array {
        $rules = [
            'name' => ['required', 'max:255',  $plat ? "unique:plats,name,{$plat->id}" : 'unique:plats'],
            "description" => "required|max:2048",
            'image' => 'required|url',
            'user_id' => 'required|exists:users,id',
        ];

        return $request->validate($rules);
    }
}
