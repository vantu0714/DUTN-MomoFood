<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
        $credentials['status'] = 1; // Chỉ cho phép tài khoản được kích hoạt

        $user = User::where('email', $credentials['email'])->first();
        if ($user && $user->status == 0) {
            return back()->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.')->withInput();
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();


            $user = Auth::user();

            if ($user->role && $user->role->name === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role && $user->role->name === 'user') {
                return redirect()->intended('/');
            } else {
                Auth::logout();
                return back()->with('error', 'Tài khoản không có quyền truy cập hợp lệ.')->withInput();
            }
        }

        return back()->with('error', 'Email hoặc mật khẩu không chính xác.')->withInput();
    }

    public function showRegister()
    {
        return view('clients.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',

            'avatar.image' => 'Ảnh đại diện phải là một hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpeg, png hoặc jpg.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 5MB.',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => $avatarPath,
            'role_id' => 2,
        ]);

        return redirect()->route('register')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
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

                $file->storeAs('public/avatar', $filename);

                $urlAvatar = 'avatar/' . $filename;
            }

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
        ], [
            'required' => 'Vui lòng nhập :attribute.',
            'min' => ':attribute phải có ít nhất :min ký tự.',
            'same' => ':attribute khớp nhau.',
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
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.exists' => 'Email không tồn tại trong hệ thống.',
        ]);

        session(['reset_email' => $request->email]);

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
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp nhau.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
        ]);

        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Phiên đặt lại mật khẩu đã hết hạn.']);
        }

        $user = User::where('email', $email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Đặt lại mật khẩu thành công!');
    }
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $products = Product::where('product_name', 'like', "%$keyword%")
            ->orWhere('product_code', 'like', "%$keyword%")
            ->orWhere('description', 'like', "%$keyword%")
            ->orWhere('ingredients', 'like', "%$keyword%")
            ->get();

        return view('products.search', compact('products', 'keyword'));
    }
}
