<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;
    protected $fillable = ['meeting_name', 'start_time', 'end_time', 'user_id'];
    public $timestamps = false;

    protected $dates = ['start_time', 'end_time'];


    public function scopeOverlappingMeetings($query, $startTime, $endTime)
    {
        return $query->where(function ($query) use ($startTime, $endTime) {
            $query->whereBetween('start_time', [$startTime, $endTime])
                ->orWhereBetween('end_time', [$startTime, $endTime])
                ->orWhere(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $startTime)
                        ->where('end_time', '>', $endTime);
                });
        });
    }
}
