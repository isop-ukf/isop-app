<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ExternalApiController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\StudentDataController;
use App\Http\Controllers\InternshipStatusDataController;
use App\Http\Middleware\AdministratorOnly;
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

Route::prefix('/account')->group(function () {
    Route::post("/activate", [RegisteredUserController::class, 'activate']);
    Route::post('/change-password', [RegisteredUserController::class, 'change_password']);
});

Route::prefix('/students')->middleware(['auth:sanctum', AdministratorOnly::class])->group(function () {
    Route::get('/', [StudentDataController::class, 'all']);
    Route::get('/{id}', [StudentDataController::class, 'get']);
    Route::post('/{id}', [StudentDataController::class, 'update_all']);
    Route::delete('/{id}', [StudentDataController::class, 'delete']);

});

Route::post('/password-reset', [RegisteredUserController::class, 'reset_password'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('password.reset');

Route::prefix('/internships')->group(function () {
    Route::get("/", [InternshipController::class, 'all'])->middleware(['auth:sanctum'])->name("api.internships");
    Route::get("/export", [InternshipController::class, 'export'])->middleware(AdministratorOnly::class)->name("api.internships.export");

    Route::prefix('/{id}')->middleware("auth:sanctum")->group(function () {
        Route::get("/", [InternshipController::class, 'get'])->name("api.internships.get");
        Route::delete("/", [InternshipController::class, 'destroy'])->middleware(AdministratorOnly::class)->name("api.internships.delete");
        Route::put("/status", [InternshipStatusDataController::class, 'update'])->name("api.internships.status.update");
        Route::get("/statuses", [InternshipStatusDataController::class, 'get'])->name("api.internships.get");
        Route::get("/next-statuses", [InternshipStatusDataController::class, 'get_next_states'])->name("api.internships.status.next.get");
        Route::get("/default-proof", [InternshipController::class, 'get_default_proof'])->name("api.internships.proof.default.get");
        Route::get("/proof", [InternshipController::class, 'get_proof'])->name("api.internships.proof.get");
        Route::get("/report", [InternshipController::class, 'get_report'])->name("api.internships.report.get");
        Route::post("/documents", [InternshipController::class, 'update_documents'])->name("api.internships.documents.set");
        Route::post("/basic", [InternshipController::class, 'update_basic'])->name("api.internships.update.basic");
    });

    Route::put("/new", [InternshipController::class, 'store'])->name("api.internships.create");
});

Route::prefix('/companies')->middleware(['auth:sanctum'])->group(function () {
    Route::get("/", [CompanyController::class, 'all']);
    Route::get("/{id}", [CompanyController::class, 'get', AdministratorOnly::class]);
    Route::post("/{id}", [CompanyController::class, 'update_all', AdministratorOnly::class]);
    Route::delete("/{id}", [CompanyController::class, 'delete', AdministratorOnly::class]);
});

Route::prefix('/external')->group(function () {
    Route::prefix('/keys')->middleware(['auth:sanctum', AdministratorOnly::class])->group(function () {
        Route::get("/", [ExternalApiController::class, 'all_keys'])->name("api.external.keys.create");
        Route::put("/", [ExternalApiController::class, 'create_key'])->name("api.external.keys.list");
        Route::delete("/{id}", [ExternalApiController::class, 'destroy_key'])->name("api.external.keys.delete");
    });


    Route::prefix('/internships')->group(function () {
        Route::prefix('/{id}')->middleware("auth:sanctum")->group(function () {
            Route::put("/status", [ExternalApiController::class, 'update_internship_status'])->name("api.external.internships.status.update");
        });
    });
});