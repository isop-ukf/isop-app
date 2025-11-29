<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Internship;
use App\Models\InternshipStatusData;
use App\Models\User;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class InternshipController extends Controller
{
    public function all()
    {
        $user = auth()->user();

        if ($user->role !== 'ADMIN') {
            abort(403, 'Unauthorized');
        }

        $internships = Internship::all();
        return response()->json($internships);
    }

    public function all_my()
    {
        $user = auth()->user();

        if ($user->role === 'STUDENT') {
            $internships = Internship::whereUserId($user->id)->get();
        } elseif ($user->role === 'EMPLOYER') {
            $company = Company::whereContact($user->id)->first();
            if (!$company) {
                return response()->json(['message' => 'No company associated with this user.'], 404);
            }
            $internships = Internship::whereCompanyId($company->id)->get();
        } else {
            abort(403, 'Unauthorized');
        }

        return response()->json($internships);
    }

    public function get(int $id)
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

        return response()->json($internship);
    }

    public function get_default_agreement(Request $request, int $id)
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

        $contact = User::find($internship->company->contact);

        $html = view('agreement.default', [
            'company' => $internship->company,
            'companyContact' => $contact,
            'internship' => $internship,
            'student' => $internship->student,
            'student_address' => $internship->student->studentData->address,
        ])->render();

        $pdf = new Mpdf([
            'orientation' => 'P'
        ]);
        $pdf->WriteHTML($html);

        return response($pdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="agreement_' . $id . '.pdf"');
    }

    public function get_agreement(int $id)
    {
        $user = auth()->user();
        $internship = Internship::find($id);

        if (!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        if (!$internship->agreement) {
            return response()->json([
                'message' => 'No agreement file exists for this internship.'
            ], 404);
        }

        if ($user->role !== 'ADMIN' && $internship->user_id !== $user->id && $user->id !== $internship->company->contact) {
            abort(403, 'Unauthorized');
        }

        return response($internship->agreement, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="agreement_' . $id . '.pdf"');
    }

    public function get_report(int $id)
    {
        $user = auth()->user();
        $internship = Internship::find($id);

        if (!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        if (!$internship->report) {
            return response()->json([
                'message' => 'No report file exists for this internship.'
            ], 404);
        }

        if ($user->role !== 'ADMIN' && $internship->user_id !== $user->id && $user->id !== $internship->company->contact) {
            abort(403, 'Unauthorized');
        }

        return response($internship->report, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report_' . $id . '.pdf"');
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

        InternshipStatusData::create([
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

        if (!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        if ($user->role !== 'ADMIN' && $internship->user_id !== $user->id && $user->id !== $internship->company->contact) {
            abort(403, 'Unauthorized');
        }

        $this->validateNewInternship($request);
        $this->checkOverlap($internship->user_id, $request->start, $request->end, $internship->id);

        $internship->update($request->except(['user_id']));
        return response()->noContent();
    }

    public function update_documents(int $id, Request $request)
    {
        $user = auth()->user();
        $internship = Internship::find($id);

        if (!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        if ($internship->user_id !== $user->id && $user->id !== $internship->company->contact) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'agreement' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'report' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'report_confirmed' => ['required', 'boolean'],
        ]);

        if ($request->hasFile('agreement')) {
            $internship->agreement = file_get_contents($request->file('agreement')->getRealPath());
        }

        if ($request->hasFile('report')) {
            $internship->report = file_get_contents($request->file('report')->getRealPath());
        }

        if ($user->role === 'EMPLOYER') {
            if ($request->report_confirmed && (!$internship->agreement || !$internship->report)) {
                return response()->json([
                    'message' => 'Report cannot be confirmed without an agreement and report.'
                ], 400);
            }

            $internship->report_confirmed = $request->report_confirmed;
        }

        $internship->save();
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

    private function validateNewInternship(Request $request)
    {
        $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
            'year_of_study' => ['required', 'integer', 'between:1,5'],
            'semester' => ['required', 'in:WINTER,SUMMER'],
            'position_description' => ['required', 'string', 'min:1']
        ]);

        $request->merge([
            'start' => date('Y-m-d 00:00:00', strtotime($request->start)),
            'end' => date('Y-m-d 00:00:00', strtotime($request->end))
        ]);
    }

    private function checkOverlap(int $user_id, string $start_date, string $end_date, ?int $current_id = null)
    {
        $existingInternship = Internship::where('user_id', $user_id)
            // check if the two internships do not have the same ID
            ->when($current_id, function ($query) use ($current_id) {
                $query->where('id', '!=', $current_id);
            })
            // check if the start/end period collides with another internship
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
