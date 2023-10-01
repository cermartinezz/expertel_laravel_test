<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeetingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\MeetingService;
use App\Models\Meeting;
use Symfony\Component\HttpFoundation\Response;

class MeetingController extends Controller
{
    public function list(): JsonResponse
    {
        return response()->json([
            'meetings' => Meeting::query()->get()
        ]);
    }


    public function create(MeetingRequest $request): JsonResponse
    {
        $service = new MeetingService;

        //add any parameters you wish
        if ($service->scheduleMeeting($request->validated()))
        {
            return response()->json(["message" => "The meeting has been booked"], Response::HTTP_CREATED);
        }
        else
        {
            return response()->json(["message" => "The meeting can not be booked"], Response::HTTP_BAD_REQUEST);
        }

    }
}
