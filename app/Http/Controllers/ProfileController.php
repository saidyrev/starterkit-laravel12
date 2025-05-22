<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rules;

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
    public function update(Request $request)
    {
        // Handle AJAX request
        if ($request->ajax()) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            ]);

            $user = $request->user();
            $user->fill($request->only(['name', 'email']));

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);
        }

        // Handle regular form submission (fallback)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);

        $user = $request->user();
        $user->fill($request->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $request->user()->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload profile picture.
     */
    public function uploadAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);

            $user = $request->user();
            
            // Delete old avatar if exists
            if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                Storage::delete('public/avatars/' . $user->avatar);
            }

            // Store new avatar
            $avatarName = time() . '.' . $request->avatar->extension();
            $request->avatar->storeAs('public/avatars', $avatarName);

            // Update user avatar
            $user->update(['avatar' => $avatarName]);

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully!',
                'avatar_url' => Storage::url('avatars/' . $avatarName)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload avatar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        if ($request->ajax()) {
            try {
                $request->validate([
                    'password' => ['required', 'current_password'],
                ]);

                $user = $request->user();

                // Delete avatar if exists
                if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                    Storage::delete('public/avatars/' . $user->avatar);
                }

                Auth::logout();
                $user->delete();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'success' => true,
                    'message' => 'Account deleted successfully!',
                    'redirect' => '/'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete account: ' . $e->getMessage()
                ], 500);
            }
        }

        // Regular form submission fallback
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}