<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = DB::selectOne(
            "SELECT * FROM users WHERE email = ?",
            [$request->email]
        );

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Email atau password salah');
        }

        session([
            'user_id'   => $user->id,
            'user_name' => $user->name
        ]);

        return redirect('/dashboard');
    }

    public function registerPage()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*[0-9])[A-Za-z0-9]+$/',
                'confirmed'
            ]
        ], [
            'password.regex' => 'Password harus mengandung minimal 1 huruf besar dan 1 angka, tanpa karakter khusus.'
        ]);

        $userId = DB::table('users')->insertGetId([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        session([
            'user_id'   => $userId,
            'user_name' => $request->name
        ]);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['user_id', 'user_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Berhasil logout');
    }

    public function profile()
    {
        $user = DB::selectOne(
            "SELECT id, name, email FROM users WHERE id = ?",
            [session('user_id')]
        );

        return view('profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'password' => [
                'nullable',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*[0-9])[A-Za-z0-9]+$/',
                'confirmed'
            ]
        ], [
            'password.regex' => 'Password harus mengandung minimal 1 huruf besar dan 1 angka, tanpa karakter khusus.'
        ]);

        if ($request->password) {
            DB::update("
                UPDATE users
                SET name = ?, password = ?
                WHERE id = ?
            ", [
                $request->name,
                Hash::make($request->password),
                session('user_id')
            ]);
        } else {
            DB::update("
                UPDATE users
                SET name = ?
                WHERE id = ?
            ", [
                $request->name,
                session('user_id')
            ]);
        }

        session(['user_name' => $request->name]);

        return back()->with('success', 'Profil berhasil diperbarui');
    }

}
