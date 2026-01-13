<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'address',
        'ico',
        'contact',
        'hiring',
        'verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified' => 'boolean',
        ];
    }

    /**
     * Get the internships for the company.
     */
    public function internships()
    {
        return $this->hasMany(Internship::class, 'company_id');
    }

    /**
     * Get the contact person (user) for the company.
     */
    public function contactPerson()
    {
        return $this->belongsTo(User::class, 'contact');
    }
}
