<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',
        'contact',
        'role',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
    public function isMahasiswa()
    {
        return $this->role === 'mahasiswa';
    }

 
    public function isPenanggungjawab()
    {
        return $this->role === 'penanggung_jawab';
    }


    public function isAdmin()
    {
        return $this->role === 'admin';
    }


    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

  
    public function penanggungjawabApprovals()
    {
        return $this->hasMany(ApprovalPenanggungjawab::class, 'approved_by');
    }


    public function adminApprovals()
    {
        return $this->hasMany(ApprovalAdmin::class, 'approved_by');
    }
}
