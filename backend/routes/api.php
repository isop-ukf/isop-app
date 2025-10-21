<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\Company;
use App\Models\StudentData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    $user = $request->user();

    $company_data = Company::whereContact($user->id)->get()->first();
    $student_data = StudentData::whereUserId($user->id)->get()->first();

    $user->company_data = $company_data;
    $user->student_data = $student_data;

    return $user;
});

Route::post('/password-reset', [RegisteredUserController::class, 'reset_password'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('password.reset');