<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('clients.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role && $user->role->name === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role && $user->role->name === 'user') {
                return redirect()->intended('/');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Tài khoản không có quyền truy cập hợp lệ.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ]);
    }

    public function showRegister()
    {
        return view('clients.auth.register');
    }

    public function register(Request $request)
    {
        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2,
        ]);

        // Đăng nhập người dùng sau khi đăng ký (nếu cần)
        auth()->login($user);

        return redirect()->intended('/login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
