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

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all(); // Lấy tất cả vai trò
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

        return redirect()->route('users.index')->with('success', 'Đã thêm người dùng thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('admin.users.show', compact('user', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
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

            return redirect()->route('users.index')->with('success', 'Cập nhật người dùng thành công');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Đã xoá người dùng');
    }

    public function toggleStatus($id)
{
    $user = User::findOrFail($id);
    $user->status = !$user->status; // Đảo trạng thái
    $user->save();

    return redirect()->route('users.index')->with('success', 'Trạng thái người dùng đã được cập nhật.');
}
}
