<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Cache\Store;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all(); // Lấy tất cả vai trò
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $request->validated();
        // dd($$request);
        $urlAvatar = null;

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Lưu vào storage/app/public/avatar
            $file->storeAs('public/avatar', $filename);

            // Đường dẫn lưu trong database: public/storage/avatar/filename
            $urlAvatar = 'avatar/' . $filename;
        }

        $users = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'avatar' => $urlAvatar,
            'status' => $request->status ?? 1,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Đã thêm người dùng thành công');
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('admin.users.show', compact('user', 'roles'));
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $request->validated();

            $urlAvatar = $user->avatar; // mặc định giữ nguyên avatar cũ

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . '_' . $file->getClientOriginalName();

                // Lưu vào storage/app/public/avatar
                $file->storeAs('public/avatar', $filename);

                // Cập nhật đường dẫn avatar
                $urlAvatar = 'avatar/' . $filename;
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'role_id' => $request->role_id,
                'avatar' => $urlAvatar,
                'status' => $request->status ?? $user->status,
            ]);

            return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() == $user->id) {
            // dd(Auth::id(), $id, $user);
            return redirect()->route('admin.users.index')
                ->with('error', 'Bạn không thể khóa tài khoản đang đăng nhập.');
        }

        $user->status = $user->status ? 0 : 1;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Đã cập nhật trạng thái tài khoản.');
    }
}
