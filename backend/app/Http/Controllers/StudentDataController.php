<?php

namespace App\Http\Controllers;

use App\Models\StudentData;
use App\Models\User;
use App\Models\InternshipStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentDataController extends Controller
{
    /**
     * Display a listing of all students with their data.
     */
    public function all()
    {
        // Iba admin môže vidieť zoznam študentov
        $user = auth()->user();

        if ($user->role !== 'ADMIN') {
            abort(403, 'Unauthorized');
        }

        $students = User::where('role', 'STUDENT')
            ->with('studentData')
            ->get();

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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone' => ['nullable', 'string', 'max:20'],
            'student_data.study_field' => ['nullable', 'string', 'max:255'],
            'student_data.personal_email' => ['nullable', 'email', 'max:255'],
            'student_data.address' => ['nullable', 'string', 'max:500'],
        ]);

        // Aktualizácia User údajov
        $student->update([
            'name' => $request->name,
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

        try {
            DB::beginTransaction();

            // 1. Získaj internship IDs
            $internshipIds = $student->internships()->pluck('id')->toArray();

            // 2. Vymaž internship statuses
            if (!empty($internshipIds)) {
                InternshipStatus::whereIn('internship_id', $internshipIds)->delete();
            }

            // 3. Vymaž internships
            $student->internships()->delete();

            // 4. Vymaž student_data
            if ($student->studentData) {
                $student->studentData()->delete();
            }

            // 5. Vymaž usera
            $student->delete();

            DB::commit();

            return response()->json([
                'message' => 'Student successfully deleted.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error deleting student.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
