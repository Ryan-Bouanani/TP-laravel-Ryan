<?php

namespace App\Http\Controllers;

use App\Mail\PublishedDish;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Plat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PlatControlller extends Controller
{
    public function index() {
        $plats = Plat::with('user', 'favoriteByUsers')->paginate(5);

        return view('welcome', compact('plats'));
    }

    public function show(Request $request, string $slug) {
        $plat = Plat::all()->where('slug', $slug)->first();
        return view('plats.show', compact("plat"));
    }

    public function edit(Request $request, string $slug) {
        $plat = Plat::all()->where('slug', $slug)->first();
        $users = User::all();
        return view('plats.edit', compact("plat", "users"));
    }

    public function update(Request $request, string $slug) {
        $plat = Plat::all()->where('slug', $slug)->first();
        $validatedData = $request->validate([
            'name' => 'required|unique:plats,name,' . $plat->id, 'max:255',
            "description" => "required|max:2048",
            'image' => 'required|url',
            'user_id' => 'required|exists:users,id',
        ]);
        $plat->update($validatedData);

        return redirect()->route('plats.index');
    }

    public function create(Request $request) {
        if (Auth::user()->hasPermissionTo('create plats')) {
            $users = User::all(); // Récupérer tous les utilisateurs pour le champ d'auteur
            return view('plats.create', compact('users'));
        } else {
            return redirect()->back()->with('danger', 'Vous n\'avez pas l\'autorisation de crée un plat');
        }
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|unique:plats,name,max:255',
            "description" => "required|max:2048",
            'image' => 'required|url',
            'user_id' => 'required|exists:users,id',
        ]);
        // Créer un nouveau plat
        Plat::create($validatedData);
        return redirect()->route('plats.index')->with('success', 'Plat créé avec succès');;
    }


    public function delete(Request $request, string $slug) {
        if (Auth::user()->hasPermissionTo('delete plats')) {
            $plat = Plat::all()->where('slug', $slug)->first();
            DB::table('plats')->delete($plat->id);
            return redirect()->back()->with('success', 'Le plats a bien été supprimé');
        } else {
            return redirect()->back()->with('danger', 'Vous n\'avez pas l\'autorisation de supprimer un plat');
        }
    }
}
