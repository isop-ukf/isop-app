<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Internship;
use App\Models\InternshipStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InternshipController extends Controller
{
    public function all()
    {
        $user = auth()->user();

        if ($user->role !== 'ADMIN') {
            abort(403, 'Unauthorized');
        }

        $internships = Internship::all()->makeHidden(['created_at', 'updated_at']);

        $internships->each(function ($internship) {
            $internship->user = User::find($internship->user_id)->makeHidden(['created_at', 'updated_at', 'email_verified_at']);
            unset($internship->user_id);
        });
        
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

        $internships->each(function ($internship) {
            $internship->start = Carbon::parse($internship->start)->format('d.m.Y');
            $internship->end = Carbon::parse($internship->end)->format('d.m.Y');
        });

        return response()->json($internships);
    }

    public function all_student()
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

        $internships->each(function ($internship) {
            $internship->start = Carbon::parse($internship->start)->format('d.m.Y');
            $internship->end = Carbon::parse($internship->end)->format('d.m.Y');
        });

        return response()->json($internships);
    }

    public function get(int $id) {
        $user = auth()->user();

        $internship = Internship::find($id);
        
        if(!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        if ($user->role !== 'ADMIN' && $internship->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $internship->company = Company::find($internship->company_id)->makeHidden(['created_at', 'updated_at']);
        unset($internship->company_id);

        $internship->contact = User::find($internship->company->contact)->makeHidden(['created_at', 'updated_at', 'email_verified_at']);
        unset($internship->company->contact);
        
        $internship->status = InternshipStatus::whereColumn('internship_id', '=', $internship->id)->get()->first()->makeHidden(['created_at', 'updated_at', 'id']);
        $internship->status->modified_by = User::find($internship->status->modified_by)->makeHidden(['created_at', 'updated_at', 'email_verified_at']);

        return response()->json($internship);
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

        $this->validateNewInternship($request);
        $this->checkOverlap($user->id, $request->start, $request->end);

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

    public function update_basic(int $id, Request $request)
    {
        $user = auth()->user();
        $internship = Internship::find($id);
        
        if(!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        if ($user->role !== 'ADMIN' && $internship->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $this->validateNewInternship($request);

        if($internship->start !== $request->start || $internship->end !== $request->end) {
            $this->checkOverlap($user->id, $request->start, $request->end);
        }

        $internship->update($request->except(['user_id']));
        return response()->noContent();
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

    private function validateNewInternship(Request $request) {
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
    }

    private function checkOverlap(int $user_id, string $start_date, string $end_date) {
        $existingInternship = Internship::where('user_id', $user_id)
            ->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('start', [$start_date, $end_date])
                    ->orWhereBetween('end', [$start_date, $end_date])
                    ->orWhere(function ($q) use ($end_date) {
                        $q->where('start', '<=', $end_date)
                          ->where('end', '>=', $end_date);
                    });
            })
            ->exists();
        
        if ($existingInternship) {
            abort(response()->json([
                'message' => 'You already have an internship during this period.'
            ], 400));
        }
    } 
}
