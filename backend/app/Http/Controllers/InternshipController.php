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
        //
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
