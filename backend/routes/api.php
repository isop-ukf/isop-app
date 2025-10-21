<?php

use App\Models\Company;
use App\Models\StudentData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    $user = $request->user();

    $company_data = Company::whereContact($user->id)->get();
    $student_data = StudentData::whereUserId($user->id)->get();

    $user->company_data = $company_data->isEmpty() ? null : $company_data;
    $user->student_data = $student_data->isEmpty() ? null : $student_data;

    return $user;
});
