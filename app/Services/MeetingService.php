<?php
namespace App\Services;

use App\Models\Meeting;
use Illuminate\Support\Facades\DB;


class MeetingService
{
    public function scheduleMeeting($data): bool
    {
        if(
          $this->validateMeeting(
            $data['users'],
            $data['start_time'],
            $data['end_time']
          )
        ){
          $meetings = $this->createMeeting(
              $data['users'],
              $data['start_time'],
              $data['end_time'],
              $data['meeting_name']);

          return count($data['users']) == count($meetings);
        }

        return false;
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

  protected function validateMeeting(mixed $users, mixed $startTime, mixed $endTime): bool
  {

      $startTime = \Carbon\Carbon::parse($startTime);
      $endTime = \Carbon\Carbon::parse($endTime);

      $overlappingMeetings = Meeting::query()
        ->whereIn('user_id', $users)
        ->where(function ($query) use ($startTime, $endTime) {
          $query->whereBetween('start_time', [$startTime, $endTime])
            ->orWhereBetween('end_time', [$startTime, $endTime])
            ->orWhere(function ($query) use ($startTime, $endTime) {
              $query->where('start_time', '<', $startTime)
                ->where('end_time', '>', $endTime);
            });
        })
        ->get();

      return ! $overlappingMeetings->count() > 0;
  }
}
