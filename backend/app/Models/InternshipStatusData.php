<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipStatusData extends Model
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $table = 'internship_statuses';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => '\App\Enums\InternshipStatus',
        ];
    }

    public function modifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'internship_id' => $this->internship_id,
            'status' => $this->status->value,
            'changed' => $this->changed,
            'note' => $this->note,
            'modified_by' => $this->modifiedBy,
        ];
    }
}
