<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetingController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::get ('/meetings', [MeetingController::class,'list'])->name('meetings.list');
Route::post('/meetings', [MeetingController::class,'create'])->name('meetings.create');
