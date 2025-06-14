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
                ])->withInput();
            }
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->withInput();
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

    public function info()
    {
        return view('clients.user.info');
    }

    public function showEditProfile()
    {
        return view('clients.user.edit');
    }

    public function editProfile(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'avatar' => 'nullable|image|max:5120',
            ]);

            $user = Auth::user();
            $urlAvatar = $user->avatar;

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . '_' . $file->getClientOriginalName();

                // Lưu vào storage/app/public/avatar
                $file->storeAs('public/avatar', $filename);

                // Cập nhật đường dẫn avatar
                $urlAvatar = 'avatar/' . $filename;
            }

            // dd($request->all());
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'avatar' => $urlAvatar,
            ]);

            return redirect()->route('clients.info')->with('success', 'Đổi thông tin thành công!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function showChangePassword()
    {
        return view('clients.user.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|min:8',
            'confirmPassword' => 'required|same:newPassword',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->currentPassword, $user->password)) {
            return back()->withErrors(['currentPassword' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->password = Hash::make($request->newPassword);
        $user->save();

        return redirect()->route('clients.info')->with('success', 'Đổi mật khẩu thành công!');
    }
}
