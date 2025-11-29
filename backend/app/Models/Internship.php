<?php

namespace App\Models;

use App\Enums\InternshipStatus;
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

    public function nextStates(string $userRole)
    {
        $current_status = $this->status->status;
        $report_confirmed = $this->report_confirmed;

        // študent nemôže meniť stav
        if ($userRole === 'STUDENT')
            return [];

        /*
            nasledujúci platný stav je určený podľa:
                - aktuálneho stavu
                - roly používateľa, ktorý ide meniť stav
                - či bol výkaz potvrdený firmou
        */
        return match (true) {
            // prax bola iba vytvorená a ide ju meniť admin alebo firma
            $current_status === InternshipStatus::SUBMITTED
            => ['CONFIRMED_BY_COMPANY', 'DENIED_BY_COMPANY'],

            // prax bola potvrdená firmou a ide ju meniť admin
            $current_status === InternshipStatus::CONFIRMED_BY_COMPANY && $userRole === "ADMIN"
            => ['CONFIRMED_BY_ADMIN', 'DENIED_BY_ADMIN'],

            // prax bola potvrdená firmou a ide ju meniť firma
            $current_status === InternshipStatus::CONFIRMED_BY_COMPANY && $userRole === "EMPLOYER"
            => ['DENIED_BY_COMPANY'],

            // prax bola zamietnutá firmou a ide ju meniť admin
            $current_status === InternshipStatus::DENIED_BY_COMPANY && $userRole === "ADMIN"
            => ['CONFIRMED_BY_COMPANY', 'SUBMITTED'],

            // prax bola potvrdená garantom, ide ju meniť admin a výkaz bol potvrdený firmou
            $current_status === InternshipStatus::CONFIRMED_BY_ADMIN && $userRole === "ADMIN" && $report_confirmed
            => ['DENIED_BY_ADMIN', 'CONFIRMED_BY_COMPANY', 'DENIED_BY_COMPANY', 'DEFENDED', 'NOT_DEFENDED'],

            // prax bola potvrdená garantom, ide ju meniť admin a výkaz nebol potvrdený firmou
            $current_status === InternshipStatus::CONFIRMED_BY_ADMIN && $userRole === "ADMIN" && !$report_confirmed
            => ['DENIED_BY_ADMIN', 'CONFIRMED_BY_COMPANY', 'DENIED_BY_COMPANY'],

            // prax bola obhájená a ide ju meniť admin
            $current_status === InternshipStatus::DEFENDED && $userRole === "ADMIN"
            => ['NOT_DEFENDED'],

            // prax nebola obhájená a ide ju meniť admin
            $current_status === InternshipStatus::NOT_DEFENDED && $userRole === "ADMIN"
            => ['DEFENDED'],

            default => []
        };
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
