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
        'hiring'
    ];

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
