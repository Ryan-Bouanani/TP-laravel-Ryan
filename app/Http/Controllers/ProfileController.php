<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Dish;
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


    /**
     * Toggle favorite dish for a user.
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function toggleFavoriteDish(Request $request, User $user): RedirectResponse {
        $dish_id = $request->input('dish_id');

        // Toggle the favorite status of the dish for the user
        $user->favoriteDishes()->toggle([$dish_id]);

        // Check if the dish is now a favorite to set the appropriate message
        $message = $user->favoriteDishes()->find($dish_id) ? 'Plat ajouté aux favoris !' : 'Plat retiré des favoris !';

        return redirect()->route('dishes.index')->with('success', $message);
    }
}
