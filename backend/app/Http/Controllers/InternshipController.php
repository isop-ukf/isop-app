<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Internship;
use App\Models\InternshipStatus;
use App\Models\User;
use Illuminate\Http\Request;

class InternshipController extends Controller
{
    public function all()
    {
        $internships = Internship::where('user_id', auth()->id())->get()->makeHidden(['created_at', 'updated_at']);
        
        $internships->each(function ($internship) {
            $internship->company = Company::find($internship->company_id)->makeHidden(['created_at', 'updated_at']);
            unset($internship->company_id);
        });

        $internships->each(function ($internship) {
            $internship->contact = User::find($internship->company->contact)->makeHidden(['created_at', 'updated_at', 'email_verified_at']);
            unset($internship->company->contact);
        });

        $internships->each(function ($internship) {
            $internship->status = InternshipStatus::whereColumn('internship_id', '=', $internship->id)->get()->first()->makeHidden(['created_at', 'updated_at', 'id']);
            $internship->status->modified_by = User::find($internship->status->modified_by)->makeHidden(['created_at', 'updated_at', 'email_verified_at']);
        });

        return response()->json($internships);
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
        $user = auth()->user();

        $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
            'year_of_study' => ['required', 'integer', 'between:1,5'],
            'semester' => ['required', 'in:WINTER,SUMMER'],
            'position_description' => ['required', 'string', 'min:1']
        ]);

        $request->merge([
            'start' => date('Y-m-d H:i:s', strtotime($request->start)),
            'end' => date('Y-m-d H:i:s', strtotime($request->end))
        ]);

        // je už nejaká prax ktorá sa časovo prekrýva?
        $existingInternship = Internship::where('user_id', $user->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start', [$request->start, $request->end])
                    ->orWhereBetween('end', [$request->start, $request->end])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start', '<=', $request->start)
                          ->where('end', '>=', $request->end);
                    });
            })
            ->exists();
        if ($existingInternship) {
            return response()->json([
                'message' => 'You already have an internship during this period.'
            ], 422);
        }

        $Internship = Internship::create([
            'user_id' => $user->id,
            'company_id' => $request->company_id,
            'start' => $request->start,
            'end' => $request->end,
            'year_of_study' => $request->year_of_study,
            'semester' => $request->semester,
            'position_description' => $request->position_description,
            'agreement' => null
        ]);

        InternshipStatus::create([
            'internship_id' => $Internship->id,
            'status' => 'SUBMITTED',
            'changed' => now(),
            'note' => null,
            'modified_by' => $user->id
        ]);

        return response()->noContent();
    }

    /**
     * Display the specified resource.
     */
    public function show(Internship $internship)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Internship $internship)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Internship $internship)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Internship $internship)
    {
        //
    }
}
