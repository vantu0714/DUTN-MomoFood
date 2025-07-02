<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                // Kiểm tra user với email đã tồn tại chưa
                $existingUser = User::where('email', $googleUser->getEmail())->first();

                if ($existingUser) {
                    // Cập nhật Google ID và avatar cho user hiện có
                    $existingUser->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $this->getHighQualityAvatar($googleUser),
                    ]);
                    $user = $existingUser;
                } else {
                    // Tạo user mới
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $this->getHighQualityAvatar($googleUser),
                        'password' => bcrypt(Str::random(16)),
                        'role_id' => 2,
                        'status' => 1,
                    ]);
                }
            } else {
                // Cập nhật avatar cho user hiện có
                $user->update([
                    'avatar' => $this->getHighQualityAvatar($googleUser),
                ]);
            }

            Auth::login($user);

            return redirect()->route('home');
        } catch (\Throwable $th) {
            return redirect()->route('login')->with('error', 'Đăng nhập Google thất bại!');
        }
    }

    /**
     * Lấy avatar chất lượng cao từ Google
     */
    private function getHighQualityAvatar($googleUser)
    {
        $avatar = $googleUser->getAvatar();

        // Thay thế size nhỏ bằng size lớn hơn
        if ($avatar) {
            $avatar = str_replace('s96-c', 's200-c', $avatar); // Tăng size từ 96px lên 200px
            $avatar = str_replace('=s96-c', '=s200-c', $avatar);
        }

        return $avatar ?: ($googleUser->avatar_original ?? null);
    }

    private function downloadAndSaveAvatar($googleUser, $user)
    {
        try {
            $avatarUrl = $this->getHighQualityAvatar($googleUser);
            if (!$avatarUrl)
                return null;

            $avatarContent = file_get_contents($avatarUrl);
            $filename = 'avatars/google_' . $user->id . '_' . time() . '.jpg';

            Storage::disk('public')->put($filename, $avatarContent);

            return Storage::url($filename);
        } catch (\Exception $e) {
            return $avatarUrl;
        }
    }

}

