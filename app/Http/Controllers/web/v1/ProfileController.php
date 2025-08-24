<?php

namespace App\Http\Controllers\web\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Timezone;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => auth()->user()]);
    }

    public function edit(Request $request)
    {
        $user = $request->user();
        $timezones = Timezone::orderBy('display_name')->get();
        return view('profile.edit', compact('timezones', 'user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . auth()->id(),
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'timezone_id' => 'required|exists:timezones,id',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048|mimes:png,jpg,webp',
            'clear_avatar' => 'nullable|boolean',
            'current_password' => 'nullable|current_password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user = auth()->user();
        $passwordChanged = false;

        if ($request->has('clear_avatar') && $request->clear_avatar) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = null;
            // if a new file is submitted, replace old one
        } elseif ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Handle original file name
            $file = $request->file('avatar');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . $originalName;

            $validated['avatar'] = $file->storeAs('avatars', $filename, 'public');
            $validated['original_avatar_name'] = $originalName;
        }

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
            $passwordChanged = true;
        } else {
            unset($validated['password']);
        }

        unset($validated['current_password']);
        $user->update($validated);

        if ($passwordChanged) {
            auth()->logout();
            return redirect()->route('login')->with('success', 'Profile updated successfully! Please login again.');
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }
}
