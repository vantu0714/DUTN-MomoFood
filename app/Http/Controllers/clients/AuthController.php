<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

            return back()->with('success', 'Cập nhật thành công!');
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

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    public function showForgotPassword()
    {
        return view('clients.auth.forgot-pass');
    }

    public function sendResetRedirect(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $email = $request->email;
        $token = Str::random(64);

        // Lưu tạm token vào session (không lưu DB, vì không dùng qua email)
        session(['reset_email' => $email, 'reset_token' => $token]);

        return redirect()->route('password.reset');
    }

    public function showResetForm()
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Vui lòng nhập email trước.']);
        }

        return view('clients.auth.reset-pass');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Phiên đặt lại mật khẩu đã hết hạn.']);
        }

        $user = User::where('email', $email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        // Xoá session để không dùng lại được
        session()->forget(['reset_email', 'reset_token']);

        return redirect('/login')->with('message', 'Đặt lại mật khẩu thành công!');
    }
}
