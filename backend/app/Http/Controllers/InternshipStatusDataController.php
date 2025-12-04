<?php

namespace App\Http\Controllers;

use App\Enums\InternshipStatus;
use App\Mail\InternshipStatusUpdated;
use App\Models\Internship;
use App\Models\InternshipStatusData;
use Illuminate\Http\Request;
use Mail;

class InternshipStatusDataController extends Controller
{
    public function get(int $id)
    {
        $user = auth()->user();
        $internship_statuses = InternshipStatusData::whereInternshipId($id)->orderByDesc('changed')->get();

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

        $nextPossibleStatuses = $internship->nextStates($user->role);
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
    public function show(InternshipStatusData $internshipStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InternshipStatusData $internshipStatus)
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
        $newStatusValidator = 'in:' . implode(',', $internship->nextStates($user->role));

        $request->validate([
            'status' => ['required', 'string', 'uppercase', $newStatusValidator],
            'note' => ['required', 'string', 'min:1']
        ]);

        $newStatus = InternshipStatusData::make([
            'internship_id' => $id,
            'status' => $request->status,
            'note' => $request->note,
            'changed' => now(),
            'modified_by' => $user->id
        ]);

        // mail študentovi
        Mail::to($internship->student)
            ->sendNow(new InternshipStatusUpdated(
                $internship,
                $internshipStatus->status,
                $newStatus->status,
                $request->note,
                $user,
                recipiantIsStudent: true,
            ));

        // ak zmenu nevykonala firma, posleme mail aj firme
        if ($user->id !== $internship->company->contactPerson->id) {
            Mail::to($internship->company->contactPerson->email)
                ->sendNow(new InternshipStatusUpdated(
                    $internship,
                    $internshipStatus->status,
                    $newStatus->status,
                    $request->note,
                    $user,
                    recipiantIsStudent: false,
                ));
        }

        $newStatus->save();
        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InternshipStatusData $internshipStatus)
    {
        //
    }
}
