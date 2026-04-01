<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'whatsapp_number' => ['nullable', 'string', 'max:25', 'regex:/^\+\d{8,15}$/'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'whatsapp_number.regex' => 'WhatsApp number must be in E.164 format, for example +923001234567.',
        ]);

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}
