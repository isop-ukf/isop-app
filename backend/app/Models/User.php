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
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'phone',
        'email',
        'role',
        'password',
        'active',
        'needs_password_change',
        'activation_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
        'created_at',
        'updated_at',
        'email_verified_at',
        'active',
        'needs_password_change'
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
            'active' => 'boolean',
            'needs_password_change' => 'boolean'
        ];
    }

    /**
     * Get the student data associated with the user.
     */
    public function studentData()
    {
        return $this->hasOne(StudentData::class, 'user_id');
    }

    /**
     * Get the student data associated with the user.
     */
    public function companyData()
    {
        return $this->hasOne(Company::class, 'contact');
    }

    /**
     * Get the internships for the user.
     */
    public function internships()
    {
        return $this->hasMany(Internship::class, 'user_id');
    }
}
