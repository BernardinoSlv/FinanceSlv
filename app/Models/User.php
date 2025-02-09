<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
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

    public function identifiers(): HasMany
    {
        return $this->hasMany(Identifier::class, 'user_id', 'id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class, 'user_id', 'id');
    }

    public function quicks(): HasMany
    {
        return $this->hasMany(Quick::class, 'user_id', 'id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'user_id', 'id');
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class, 'user_id', 'id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'user_id', 'id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, "user_id", "id");
    }
}
