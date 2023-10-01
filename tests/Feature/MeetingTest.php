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
    /**
     * A basic feature test example.
     *
     * @return void
     */
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


}
