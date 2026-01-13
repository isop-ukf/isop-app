<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Internship;
use App\Models\InternshipStatusData;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function all(Request $request)
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:-1|max:100',
        ]);

        $perPage = $request->input('per_page', 15);

        // Handle "All" items (-1)
        if ($perPage == -1) {
            $perPage = Company::count();
        }

        $companies = Company::query();

        // hide unverified companies from students
        if (Auth::user()->role != 'ADMIN') {
            $companies = $companies->whereVerified(true);
        }

        $companies = $companies->paginate($perPage);
        $companies->getCollection()->transform(function ($company) {
            $company->contact = $company->contactPerson;
            return $company;
        });

        return response()->json($companies);
    }

    /**
     * Get a specific company with contact details.
     */
    public function get(int $id)
    {
        $user = auth()->user();

        if ($user->role !== 'ADMIN') {
            abort(403, 'Unauthorized');
        }

        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'message' => 'No such company exists.'
            ], 400);
        }

        $company->contact = User::find($company->contact);

        return response()->json($company);
    }

    /**
     * Update company information and contact person.
     */
    public function update_all(int $id, Request $request)
    {
        $user = auth()->user();

        if ($user->role !== 'ADMIN') {
            abort(403, 'Unauthorized');
        }

        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'message' => 'No such company exists.'
            ], 400);
        }

        // Validácia dát
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'ico' => ['required', 'integer'],
            'hiring' => ['required', 'boolean'],
            'contact.first_name' => ['required', 'string', 'max:255'],
            'contact.last_name' => ['required', 'string', 'max:255'],
            'contact.email' => ['required', 'email', 'max:255', 'unique:users,email,' . $company->contact],
            'contact.phone' => ['nullable', 'string', 'max:20'],
        ]);

        // Aktualizácia Company údajov
        $company->update([
            'name' => $request->name,
            'address' => $request->address,
            'ico' => $request->ico,
            'hiring' => $request->hiring,
        ]);

        // Aktualizácia kontaktnej osoby
        if ($request->has('contact')) {
            $contactPerson = User::find($company->contact);

            if ($contactPerson) {
                $contactPerson->update([
                    'first_name' => $request->contact['first_name'],
                    'last_name' => $request->contact['last_name'],
                    'name' => $request->contact['first_name'] . ' ' . $request->contact['last_name'],
                    'email' => $request->contact['email'],
                    'phone' => $request->contact['phone'] ?? null,
                ]);
            }
        }

        return response()->noContent();
    }

    public function update_verification(int $id, Request $request)
    {
        $request->validate([
            'status' => ['required', 'boolean']
        ]);

        $company = Company::find($id);

        $company->update([
            'verified' => $request->status
        ]);

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
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        //
    }

    /**
     * Delete a company, its contact person and all related data.
     */
    public function delete(int $id)
    {
        $user = auth()->user();

        // Admin kontrola
        if ($user->role !== 'ADMIN') {
            abort(403, 'Unauthorized');
        }

        $company = Company::find($id);
        $company_contact = User::find($company->contact);

        if (!$company) {
            return response()->json([
                'message' => 'No such company exists.'
            ], 400);
        }

        DB::beginTransaction();

        $internships = Internship::whereCompanyId($company->id);

        // mazanie statusov
        $internships->each(function ($internship) {
            InternshipStatusData::whereInternshipId($internship->id)->delete();
        });

        // mazanie praxov
        $internships->delete();

        // mazanie firmy
        Company::whereContact($company_contact->id);

        // mazanie účtu firmy
        $company_contact->delete();

        DB::commit();

        return response()->noContent();
    }
}
