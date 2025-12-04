<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Models\StudentData;
use App\Models\User;
use App\Models\InternshipStatusData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentDataController extends Controller
{
    /**
     * Display a listing of all students with their data.
     */
    public function all(Request $request)
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:-1|max:100',
        ]);

        $perPage = $request->input('per_page', 15);

        // Handle "All" items (-1)
        if ($perPage == -1) {
            $perPage = User::whereRole('STUDENT')->count();
        }

        $students = User::query()
            ->whereRole('STUDENT')
            ->with(['studentData'])
            ->paginate($perPage);

        return response()->json($students);
    }

    /**
     * Get a specific student with their data.
     */
    public function get(int $id)
    {
        $user = auth()->user();

        $student = User::find($id);

        if (!$student) {
            return response()->json([
                'message' => 'No such student exists.'
            ], 400);
        }

        if ($student->role !== 'STUDENT') {
            return response()->json([
                'message' => 'User is not a student.'
            ], 400);
        }

        if ($user->role !== 'ADMIN') {
            abort(403, 'Unauthorized');
        }

        $student->load('studentData');

        return response()->json($student);
    }

    /**
     * Update student's basic information and student data.
     */
    public function update_all(int $id, Request $request)
    {
        $user = auth()->user();

        $student = User::find($id);

        if (!$student) {
            return response()->json([
                'message' => 'No such student exists.'
            ], 400);
        }

        if ($student->role !== 'STUDENT') {
            return response()->json([
                'message' => 'User is not a student.'
            ], 400);
        }

        if ($user->role !== 'ADMIN') {
            abort(403, 'Unauthorized');
        }

        // Validácia dát
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone' => ['nullable', 'string', 'max:20'],
            'student_data.study_field' => ['nullable', 'string', 'max:255'],
            'student_data.personal_email' => ['nullable', 'email', 'max:255'],
            'student_data.address' => ['nullable', 'string', 'max:500'],
        ]);

        // Aktualizácia User údajov
        $student->update([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Aktualizácia alebo vytvorenie StudentData
        if ($request->has('student_data')) {
            $studentData = $student->studentData;

            if ($studentData) {
                $studentData->update($request->student_data);
            } else {
                $student->studentData()->create($request->student_data);
            }
        }

        return response()->noContent();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentData $studentData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentData $studentData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentData $studentData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentData $studentData)
    {
        //
    }

    /**
     * Delete a student and all related data.
     */
    public function delete(int $id)
    {
        $user = auth()->user();

        // Admin kontrola
        if ($user->role !== 'ADMIN') {
            abort(403, 'Unauthorized');
        }

        $student = User::find($id);

        if (!$student) {
            return response()->json([
                'message' => 'No such student exists.'
            ], 400);
        }

        if ($student->role !== 'STUDENT') {
            return response()->json([
                'message' => 'User is not a student.'
            ], 400);
        }

        DB::beginTransaction();

        // mazanie praxov
        $internships = Internship::whereUserId($student->id);

        // mazanie statusov
        $internships->each(function ($internship) {
            InternshipStatusData::whereInternshipId($internship->id)->delete();
        });

        // mazanie praxov
        $internships->delete();

        // mazanie firmy
        StudentData::whereUserId($student->id);

        // mazanie účtu firmy
        $student->delete();

        DB::commit();

        return response()->noContent();
    }
}
