<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Plat;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function addFavoritePlatToUser(Request $request, int $userId): RedirectResponse {
        $plat_id = $request->input('plat_id');
        $user = User::find($userId);

        // Si l'utilisateur à déjà le plats en favoris alors on l'enlève sinon on l'ajoute
        if ($user->favoritePlats()->find($plat_id)) {
            $user->favoritePlats()->detach([$plat_id]);
            $message = 'Plat retiré des favoris !';
        } else {
            $user->favoritePlats()->attach([$plat_id]);
            $message = 'Plat ajouté des favoris !';
        }
        return redirect()->back()->with('success', $message);
    }
}
