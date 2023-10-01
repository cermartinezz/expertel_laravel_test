<?php
namespace App\Services;

use App\Models\Meeting;
use Carbon\Carbon;
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

      $startTime = Carbon::parse($startTime);
      $endTime = Carbon::parse($endTime);

      $overlappingMeetings = Meeting::query()
        ->whereIn('user_id', $users)
        ->overlappingMeetings($startTime, $endTime)
        ->get();

      return ! $overlappingMeetings->count() > 0;
  }
}
