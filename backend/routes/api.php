<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InternshipController;
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

Route::prefix('/internships')->group(function () {
    Route::get("/", [InternshipController::class, 'all'])->name("api.internships");

    Route::middleware("auth:sanctum")->group(function () {
        Route::put("/new", [InternshipController::class, 'store'])->name("api.internships.create");
    });
});

Route::prefix('/companies')->middleware("auth:sanctum")->group(function () {
    Route::get("/simple", [CompanyController::class, 'all_simple']);
});