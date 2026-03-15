<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Transactions on this user's pages (via page_id). Use this for revenue;
     * transactions.user_id is not set on create.
     */
    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Page::class);
    }

    public function paymentGateways()
    {
        return $this->hasMany(PaymentGateway::class);
    }

    public function settings()
    {
        return $this->hasMany(AdminSetting::class);
    }
}
