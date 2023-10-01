<?php
namespace App\Services;

use App\Models\Meeting;
use Illuminate\Support\Facades\DB;


class MeetingService
{
    public function scheduleMeeting($data): bool
    {

        $meetings = $this->createMeeting(
            $data['users'],
            $data['start_time'],
            $data['end_time'],
            $data['meeting_name']);

        return count($data['users']) == count($meetings);

    }

    protected function createMeeting($users, $startTime, $endTime, $name)
    {
        return DB::transaction(function () use ($users, $startTime, $endTime, $name){
          return array_map(function($userId) use ($startTime, $endTime, $name){
            return Meeting::query()->create([
              'user_id' => $userId,
              'start_time' => $startTime,
              'end_time' => $endTime,
              'meeting_name' => $name,
            ]);
          }, $users);
        });
    }

}
