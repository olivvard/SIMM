<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(){
        return view('user.profile');
    }

    public function updatePicture(Request $request)
    {
        $request->validate([
            'picture' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old picture if it exists and is a file
        if ($user->picture) {
            $oldPath = public_path($user->picture);
            if (file_exists($oldPath) && is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $file = $request->file('picture');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/profiles'), $filename);

        $user->update([
            'picture' => 'uploads/profiles/' . $filename,
        ]);

        return back()->with('success', 'Profile picture updated successfully!');
    }
}
