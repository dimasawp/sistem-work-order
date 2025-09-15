<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
    public function profile() {
        return view('pages.profile');
    }

    // public function updateEmail(Request $request) {
    //     $request->validate([
    //         'email' => 'required|email|unique:users,email,' . auth()->id(),
    //     ]);

    //     $user = auth()->user();
    //     $user->email = $request->email;
    //     $user->save();

    //     return back()->with('success', 'Email berhasil diperbarui.');
    // }

    public function updatePassword(Request $request) {
        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
