<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'time_entry_id',
        'start_time',
        'end_time',
        'start_location',
        'end_location',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function timeEntry()
    {
        return $this->belongsTo(TimeEntry::class);
    }
}
