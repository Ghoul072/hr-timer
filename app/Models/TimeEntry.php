<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\BreakEntry;

class TimeEntry extends Model
{
    use HasFactory;

     protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'start_location',
        'end_location',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakEntry::class);
    }
}
