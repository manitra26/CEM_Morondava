<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        return view('profile.show', compact('user'));
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'position' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'domicile' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'theme' => ['required', 'in:light,dark,system'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::delete($user->avatar_path);
            }
            $data['avatar_path'] = $request->file('avatar')->store('profiles');
        }

        unset($data['avatar']);
        $user->update($data);

        return back()->with('success', 'Votre profil a été mis à jour.');
    }

    public function avatar(User $user): Response|StreamedResponse
    {
        abort_unless($user->avatar_path && Storage::exists($user->avatar_path), 404);

        return Storage::response($user->avatar_path);
    }
}
