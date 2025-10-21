<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\StudentData;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $password = bin2hex(random_bytes(16));

        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'first_name' => ['required', 'string', 'max:64'],
            'last_name' => ['required', 'string', 'max:64'],
            'phone' => ['required', 'string', 'max:13'],
            'role' => ['required', 'string', 'uppercase', 'in:STUDENT,EMPLOYER'],

            // študentské info
            'student_data' => ['required_if:role,STUDENT', 'array'],
            'student_data.address' => ['required_if:role,STUDENT', 'string', 'max:64'],
            'student_data.personal_email' => ['required_if:role,STUDENT', 'string', 'email'],
            'student_data.study_field' => ['required_if:role,STUDENT', 'string', 'max:32'],

            // firemné info
            'company_data' => ['required_if:role,EMPLOYER', 'array'],
            'company_data.name' => ['required_if:role,EMPLOYER', 'string', 'max:64'],
            'company_data.address' => ['required_if:role,EMPLOYER', 'string', 'max:64'],
            'company_data.ico' => ['required_if:role,EMPLOYER', 'integer'],
            'company_data.hiring' => ['required_if:role,EMPLOYER', 'boolean'],
        ]);

        $user = User::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => "{$request->first_name} {$request->last_name}",
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => Hash::make($password),
        ]);

        if($user->role === "STUDENT") {
            StudentData::create([
                'user_id' => $user->id,
                'address' => $request->student_data['address'],
                'personal_email' => $request->student_data['personal_email'],
                'study_field' => $request->student_data['study_field'],
            ]);
        } else if($user->role === "EMPLOYER") {
            Company::create([
                'name' => $request->company_data['name'],
                'address' => $request->company_data['address'],
                'ico' => $request->company_data['ico'],
                'contact' => $user->id,
                'hiring' => $request->company_data['hiring'],
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return response()->noContent();
    }
}
