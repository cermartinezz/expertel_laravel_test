<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MeetingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_list_meetings()
    {
      $users = User::factory()->count(2)->create();

      $startTime = now()->addHour();

      $endTime = now()->addHour(2);

      $meetingName = 'Test Meeting';

      $this->postJson(route('meetings.create'), [
        'users' => $users->pluck('id')->toArray(),
        'start_time' => $startTime->toDateTimeString(),
        'end_time' => $endTime->toDateTimeString(),
        'meeting_name' => $meetingName,
      ]);

      $response = $this->getJson(route('meetings.list'));

      $response->assertJsonCount(count($users), 'meetings');

    }
    public function test_meeting_can_be_created()
    {
        $users = User::factory()->count(2)->create();

        $startTime = now()->addHour();

        $endTime = now()->addHour(2);

        $meetingName = 'Test Meeting';

        $response = $this->postJson(route('meetings.create'), [
            'users' => $users->pluck('id')->toArray(),
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'meeting_name' => $meetingName,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson(['message'=>'The meeting has been booked']);
    }

    public function test_cant_book_a_meeting_if_user_is_busy_at_new_meeting_time()
    {
      // First create a meeting
      $users = User::factory()->count(2)->create();

      $startTime = now()->addHour();

      $endTime = now()->addHour(2);

      $meetingName = 'Test Meeting';

      $response = $this->postJson(route('meetings.create'), [
        'users' => $users->pluck('id')->toArray(),
        'start_time' => $startTime->toDateTimeString(),
        'end_time' => $endTime->toDateTimeString(),
        'meeting_name' => $meetingName,
      ]);

      // This should be successful
      $response->assertStatus(Response::HTTP_CREATED);

      // Then try to create another meeting with the same users to the same time
      $startTime = now()->addHour();

      $endTime = now()->addHour(2);

      $meetingName = 'Test Meeting 2';

      $response2 = $this->postJson(route('meetings.create'), [
        'users' => $users->pluck('id')->toArray(),
        'start_time' => $startTime->toDateTimeString(),
        'end_time' => $endTime->toDateTimeString(),
        'meeting_name' => $meetingName,
      ]);

      // This should fail
      $response2->assertStatus(Response::HTTP_BAD_REQUEST);
      $response2->assertJson(['message'=>'The meeting can not be booked']);

    }


    public function test_can_book_a_meeting_if_user_is_not_busy_at_new_meeting_time()
    {
      // First create a meeting
      $users = User::factory()->count(2)->create();

      $startTime = now()->addHour();

      $endTime = now()->addHour(2);

      $meetingName = 'Test Meeting';

      $response = $this->postJson(route('meetings.create'), [
        'users' => $users->pluck('id')->toArray(),
        'start_time' => $startTime->toDateTimeString(),
        'end_time' => $endTime->toDateTimeString(),
        'meeting_name' => $meetingName,
      ]);

      // This should be successful
      $response->assertStatus(Response::HTTP_CREATED);

      // Then try to create another meeting with the same users
      $startTime = now()->addHour(4);

      $endTime = now()->addHour(5);

      $meetingName = 'Test Meeting 2';

      $response2 = $this->postJson(route('meetings.create'), [
        'users' => $users->pluck('id')->toArray(),
        'start_time' => $startTime->toDateTimeString(),
        'end_time' => $endTime->toDateTimeString(),
        'meeting_name' => $meetingName,
      ]);

      // This should be successful
      $response2->assertStatus(Response::HTTP_CREATED);
    }
}
