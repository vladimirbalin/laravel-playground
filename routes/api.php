<?php

use App\Http\Controllers\ScheduleRetrieveController;
use App\Http\Controllers\ScheduleUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('/schedule', ScheduleRetrieveController::class);
Route::post('/schedule', ScheduleUpdateController::class);
