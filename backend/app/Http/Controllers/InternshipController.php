<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Models\InternshipStatusData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class InternshipController extends Controller
{
    public function all(Request $request)
    {
        $internships = $this->filterSearch($request);
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

    public function get_default_proof(Request $request, int $id)
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

        $html = view('proof.default', [
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
            ->header('Content-Disposition', 'attachment; filename="proof_' . $id . '.pdf"');
    }

    public function get_proof(int $id)
    {
        $user = auth()->user();
        $internship = Internship::find($id);

        if (!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        if (!$internship->proof) {
            return response()->json([
                'message' => 'No proof file exists for this internship.'
            ], 404);
        }

        if ($user->role !== 'ADMIN' && $internship->user_id !== $user->id && $user->id !== $internship->company->contact) {
            abort(403, 'Unauthorized');
        }

        return response($internship->proof, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="proof_' . $id . '.pdf"');
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

    public function export(Request $request)
    {
        $internships = $this->filterSearch($request, true);

        $csv_header = [
            'ID',
            'Student',
            'Company',
            'Start',
            'End',
            'YearOfStudy',
            'Semester',
            'StudyField',
            'Status'
        ];

        $csv_content = implode(',', $csv_header) . "\n";
        foreach ($internships as $internship) {
            $data = [
                $internship->id,
                '"' . $internship->student->name . '"',
                '"' . $internship->company->name . '"',
                Carbon::parse($internship->start)->format('d.m.Y'),
                Carbon::parse($internship->end)->format('d.m.Y'),
                $internship->year_of_study,
                $internship->semester,
                $internship->student->studentData->study_field,
                $internship->status->status->value
            ];
            $csv_content .= implode(',', $data) . "\n";
        }

        return response($csv_content, 200)
            ->header('Content-Type', 'application/csv')
            ->header('Content-Disposition', 'attachment; filename="internships.csv"');
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
            'proof' => null
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
            'proof' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'report' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'report_confirmed' => ['required', 'boolean'],
        ]);

        if ($request->hasFile('proof')) {
            $internship->proof = file_get_contents($request->file('proof')->getRealPath());
        }

        if ($request->hasFile('report')) {
            $internship->report = file_get_contents($request->file('report')->getRealPath());
        }

        if ($user->role === 'EMPLOYER') {
            if ($request->report_confirmed && (!$internship->proof || !$internship->report)) {
                return response()->json([
                    'message' => 'Report cannot be confirmed without an proof and report.'
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
    public function destroy(int $id)
    {
        $internship = Internship::find($id);

        if (!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }
        $internship->delete();

        return response()->noContent();
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

    private function filterSearch(Request $request, bool $ignorePage = false)
    {
        $user = $request->user();

        $request->validate([
            'year' => 'nullable|integer',
            'company' => 'nullable|string|min:3|max:32',
            'study_programe' => 'nullable|string|min:3|max:32',
            'student' => 'nullable|string|min:3|max:32',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:-1|max:100',
        ]);

        if ($ignorePage) {
            $request->merge(['per_page' => -1]);
        }

        $perPage = $request->input('per_page', 15);

        // Handle "All" items (-1)
        if ($perPage == -1) {
            $perPage = Internship::count();
        }

        $internships = Internship::query()
            ->with(['student.studentData'])
            ->when($request->year, function ($query, $year) {
                $query->whereYear('start', $year);
            })
            ->when($request->company, function ($query, $company) {
                $query->whereHas('company', function ($q) use ($company) {
                    $q->where('name', 'like', "%$company%");
                });
            })
            ->when($request->study_programe, function ($query, $studyPrograme) {
                $query->whereHas('student.studentData', function ($q) use ($studyPrograme) {
                    $q->where('study_field', 'like', "%$studyPrograme%");
                });
            })
            ->when($request->student, function ($query, $student) {
                $query->whereHas('student', function ($q) use ($student) {
                    $q->where('name', 'like', "%$student%");
                });
            })
            ->when($user->role === 'STUDENT', function ($query) use ($user) {
                $query->where('user_id', '=', $user->id);
            })
            ->when($user->role === 'EMPLOYER', function ($query) use ($user) {
                $query->whereHas('company', function ($q) use ($user) {
                    $q->where('contact', 'like', $user->id);
                });
            })
            ->paginate($perPage);

        return $internships;
    }
}
