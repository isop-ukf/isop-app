<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function all_simple()
    {
        $companies = Company::all()->makeHidden(['created_at', 'updated_at']);

        $companies->each(function ($company) {
            $company->contact = User::find($company->contact)->makeHidden(['created_at', 'updated_at', 'email_verified_at']);
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

        $company->contact = User::find($company->contact)->makeHidden(['created_at', 'updated_at', 'email_verified_at']);

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
            'contact.name' => ['required', 'string', 'max:255'],
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
                    'name' => $request->contact['name'],
                    'email' => $request->contact['email'],
                    'phone' => $request->contact['phone'] ?? null,
                ]);
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
}
