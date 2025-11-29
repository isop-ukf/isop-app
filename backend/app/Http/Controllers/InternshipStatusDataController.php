<?php

namespace App\Http\Controllers;

use App\Mail\InternshipStatusUpdated;
use App\Models\Internship;
use App\Models\InternshipStatus;
use Illuminate\Http\Request;
use Mail;

class InternshipStatusDataController extends Controller
{
    public function get(int $id)
    {
        $user = auth()->user();
        $internship_statuses = InternshipStatus::whereInternshipId($id)->orderByDesc('changed')->get();

        if (!$internship_statuses) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        $internship = Internship::find($id);
        if ($user->role !== 'ADMIN' && $internship->user_id !== $user->id && $user->id !== $internship->company->contact) {
            abort(403, 'Unauthorized');
        }

        return response()->json($internship_statuses);
    }

    public function get_next_states(int $id)
    {
        $user = auth()->user();
        $internship = Internship::find($id);

        if (!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        if ($user->role !== 'ADMIN' && $internship->user_id !== $user->id && $user->id !== $internship->company->contact) {
            abort(403, 'Unauthorized');
        }

        $currentStatus = $internship->status;
        $nextPossibleStatuses = $this->possibleNewStatuses($currentStatus->status, $user->role, $internship->report_confirmed);

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

        if (!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        if ($user->role !== 'ADMIN' && $user->id !== $internship->company->contact) {
            abort(403, 'Unauthorized');
        }

        $internshipStatus = $internship->status;
        $newStatusValidator = 'in:' . implode(',', $this->possibleNewStatuses($internshipStatus->status, $user->role, $internship->report_confirmed));

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

        Mail::to($internship->student)->sendNow(new InternshipStatusUpdated($internship, $user->name, $internship->student->name, $internship->company->name, $internshipStatus->status, $request->status, $request->note));

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InternshipStatus $internshipStatus)
    {
        //
    }

    private function possibleNewStatuses(string $current_status, string $userRole, bool $report_confirmed)
    {
        if ($userRole === "STUDENT")
            return [];

        switch ($current_status) {
            case 'SUBMITTED':
                return ['CONFIRMED', 'DENIED'];
            case 'CONFIRMED':
                if ($userRole === 'EMPLOYER') {
                    return ['DENIED'];
                }

                if ($report_confirmed) {
                    return ['SUBMITTED', 'DENIED', 'DEFENDED', 'NOT_DEFENDED'];
                }

                return ['SUBMITTED', 'DENIED'];
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
}
