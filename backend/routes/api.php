<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\StudentDataController;
use App\Http\Controllers\InternshipStatusController;
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

Route::middleware(['auth:sanctum'])->prefix('/students')->group(function () {
    Route::get('/', [StudentDataController::class, 'all']);
    Route::get('/{id}', [StudentDataController::class, 'get']);
    Route::post('/{id}', [StudentDataController::class, 'update_all']);
    Route::delete('/{id}', [StudentDataController::class, 'delete']);
});

Route::post('/password-reset', [RegisteredUserController::class, 'reset_password'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('password.reset');

Route::prefix('/internships')->group(function () {
    Route::get("/", [InternshipController::class, 'all'])->name("api.internships");
    Route::get("/my", [InternshipController::class, 'all_student'])->name("api.internships.student");

    Route::middleware("auth:sanctum")->group(function () {
        Route::prefix('/{id}')->group(function () {
            Route::get("/", [InternshipController::class, 'get'])->name("api.internships.get");
            Route::put("/status", [InternshipStatusController::class, 'update'])->name("api.internships.status.update");
            Route::get("/statuses", [InternshipStatusController::class, 'get'])->name("api.internships.get");
            Route::get("/next-statuses", [InternshipStatusController::class, 'get_next_states'])->name("api.internships.status.next.get");
            Route::post("/basic", [InternshipController::class, 'update_basic'])->name("api.internships.update.basic");
        });

        Route::put("/new", [InternshipController::class, 'store'])->name("api.internships.create");
    });
});

Route::prefix('/companies')->middleware("auth:sanctum")->group(function () {
    Route::get("/simple", [CompanyController::class, 'all_simple']);
    Route::get("/{id}", [CompanyController::class, 'get']);
    Route::post("/{id}", [CompanyController::class, 'update_all']);
    Route::delete("/{id}", [CompanyController::class, 'delete']);
});