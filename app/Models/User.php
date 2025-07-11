<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Recipient;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'password',
        'role_id',
        'phone',
        'address',
        'avatar',
        'status',
        'is_vip'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Accessor để lấy avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            // Nếu là URL từ Google
            if (str_contains($this->avatar, 'googleusercontent.com')) {
                return $this->avatar;
            }

            // Nếu là file local
            if (str_starts_with($this->avatar, '/storage/')) {
                return asset($this->avatar);
            }

            // Nếu là path trong storage
            return asset('storage/' . $this->avatar);
        }

        // Avatar mặc định
        return $this->getDefaultAvatar();
    }

    /**
     * Tạo avatar mặc định từ tên
     */
    public function getDefaultAvatar()
    {
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF";
    }

    // public function
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function recipients()
    {
        return $this->hasMany(Recipient::class);
    }

    public function defaultRecipient()
    {
        return $this->hasOne(Recipient::class)->where('is_default', true);
    }
}
