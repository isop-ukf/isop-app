<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    /** @use HasFactory<\Database\Factories\InternshipFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'start',
        'end',
        'year_of_study',
        'semester',
        'position_description',
        'agreement',
        'report',
        'report_confirmed',
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
            'report_confirmed' => 'boolean',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function status()
    {
        return $this->hasOne(InternshipStatusData::class, 'internship_id')->latestOfMany();
    }

    /**
     * Prepare the model for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'student' => $this->student,
            'company' => $this->company,
            'start' => Carbon::parse($this->start)->format('d.m.Y'),
            'end' => Carbon::parse($this->end)->format('d.m.Y'),
            'year_of_study' => $this->year_of_study,
            'semester' => $this->semester,
            'position_description' => $this->position_description,
            'agreement' => $this->agreement !== null,
            'report' => $this->report !== null,
            'report_confirmed' => $this->report_confirmed,
            'status' => $this->status,
        ];
    }
}
