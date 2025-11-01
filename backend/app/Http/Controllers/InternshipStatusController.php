<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Models\InternshipStatus;
use App\Models\User;
use Illuminate\Http\Request;

class InternshipStatusController extends Controller
{
    public function get(int $id) {
        $user = auth()->user();
        $internship_statuses = InternshipStatus::whereInternshipId($id)->get()->makeHidden(['created_at', 'updated_at', 'id']);

        if(!$internship_statuses) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        $internship = Internship::where($id);
        if ($user->role !== 'ADMIN' && $internship->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $internship_statuses->each(function ($internship_status) {
            $internship_status->modified_by = User::find($internship_status->modified_by)->makeHidden(['created_at', 'updated_at', 'email_verified_at']);
        });

        return response()->json($internship_statuses);
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
    public function show(InternshipStatus $internshipStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InternshipStatus $internshipStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InternshipStatus $internshipStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InternshipStatus $internshipStatus)
    {
        //
    }
}
