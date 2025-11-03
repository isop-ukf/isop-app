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
        $internship_statuses = InternshipStatus::whereInternshipId($id)->orderByDesc('changed')->get()->makeHidden(['created_at', 'updated_at', 'id']);

        if(!$internship_statuses) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        $internship = Internship::find($id);
        if ($user->role !== 'ADMIN' && $internship->user_id !== $user->id && $user->id !== $internship->company->contact) {
            abort(403, 'Unauthorized');
        }

        $internship_statuses->each(function ($internship_status) {
            $internship_status->modified_by = User::find($internship_status->modified_by)->makeHidden(['created_at', 'updated_at', 'email_verified_at']);
        });

        return response()->json($internship_statuses);
    }

    public function get_next_states(int $id) {
        $user = auth()->user();
        $internship = Internship::find($id);

        if(!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        if ($user->role !== 'ADMIN' && $internship->user_id !== $user->id && $user->id !== $internship->company->contact) {
            abort(403, 'Unauthorized');
        }

        $currentStatus = $this->currentInternshipStatus($internship);
        $nextPossibleStatuses = $this->possibleNewStatuses($currentStatus->status, $user->role);

        return response()->json($nextPossibleStatuses);
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
    public function update(int $id, Request $request)
    {
        $user = auth()->user();
        $internship = Internship::find($id);

        if(!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        if ($user->role !== 'ADMIN' && $user->id !== $internship->company->contact) {
            abort(403, 'Unauthorized');
        }

        $internshipStatus = $this->currentInternshipStatus($internship);
        $newStatusValidator = 'in:' . implode(',', $this->possibleNewStatuses($internshipStatus->status, $user->role));

        $request->validate([
            'status' => ['required', 'string', 'uppercase', $newStatusValidator],
            'note' => ['required', 'string', 'min:1']
        ]);

        InternshipStatus::create([
            'internship_id' => $id,
            'status' => $request->status,
            'note' => $request->note,
            'changed' => now(),
            'modified_by' => $user->id
        ]);

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InternshipStatus $internshipStatus)
    {
        //
    }

    private function possibleNewStatuses(string $current_status, string $userRole) {
        if($userRole === "STUDENT") return [];

        switch ($current_status) {
            case 'SUBMITTED':
                return ['CONFIRMED', 'DENIED'];
            case 'CONFIRMED':
                if ($userRole === 'EMPLOYER') {
                    return ['DENIED'];
                }
                return ['SUBMITTED', 'DENIED', 'DEFENDED', 'NOT_DEFENDED'];
            case 'DENIED':
                if ($userRole === 'EMPLOYER') {
                    return ['CONFIRMED'];
                }
                return ['SUBMITTED', 'CONFIRMED'];
            case 'DEFENDED':
            case 'NOT_DEFENDED':
                return [];
            default:
                throw new \InvalidArgumentException('Unknown status');
        }
    }

    private function currentInternshipStatus(Internship $internship) {
        return InternshipStatus::whereInternshipId($internship->id)->orderByDesc('changed')->firstOrFail();
    }
}
