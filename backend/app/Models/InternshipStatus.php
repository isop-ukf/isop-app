<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipStatus extends Model
{
    /** @use HasFactory<\Database\Factories\InternshipStatusFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'internship_id',
        'status',
        'changed',
        'note',
        'modified_by'
    ];
}
