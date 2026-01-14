<?php

namespace App\Http\Controllers;

use App\Enums\InternshipStatus;
use App\Mail\InternshipStatusUpdated;
use App\Models\Internship;
use App\Models\InternshipStatusData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Mail;

class ExternalApiController extends Controller
{
    public function all_keys(Request $request)
    {
        $tokens = Sanctum::$personalAccessTokenModel::with('tokenable')->get();

        $tokens = $tokens->map(fn($token) => [
            "id" => $token->id,
            "name" => $token->name,
            "created_at" => Carbon::parse($token->created_at)->format('d.m.Y H:i:s'),
            "last_used_at" => $token->last_used_at ? Carbon::parse($token->last_used_at)->format('d.m.Y H:i:s') : null,
            "owner" => User::find($token->tokenable_id)->name,
        ]);

        return response()->json($tokens);
    }

    public function create_key(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:64',
        ]);

        if (Sanctum::$personalAccessTokenModel::where('name', $request->name)->exists()) {
            return response()->json([
                'message' => 'A token with this name already exists.'
            ], 422);
        }

        $token = $request->user()->createToken($request->name)->plainTextToken;

        return response()->json([
            "key" => $token,
        ]);
    }

    public function destroy_key(Request $request, int $id)
    {
        $request->user()->tokens()->where('id', $id)->delete();
        return response()->noContent();
    }

    public function update_internship_status(Request $request, $id)
    {
        $user = $request->user();
        $internship = Internship::find($id);

        if (!$internship) {
            return response()->json([
                'message' => 'No such internship exists.'
            ], 400);
        }

        $currentStatus = $internship->status->status;

        $request->validate([
            'status' => ['required', 'string', 'uppercase', 'in:DEFENDED,NOT_DEFENDED'],
            'note' => ['required', 'string', 'min:1']
        ]);

        if ($currentStatus !== InternshipStatus::CONFIRMED_BY_ADMIN) {
            return response()->json([
                "error" => "Expected current status to be 'CONFIRMED_BY_ADMIN', but it was '$currentStatus->value' instead",
            ], 422);
        }

        $newStatus = InternshipStatusData::make([
            'internship_id' => $id,
            'status' => $request->status,
            'note' => $request->note,
            'changed' => now(),
            'modified_by' => $user->id,
        ]);

        // mail študentovi
        Mail::to($internship->student)
            ->sendNow(new InternshipStatusUpdated(
                    $internship,
                    $currentStatus,
                    $newStatus->status,
                    $request->note,
                    $user,
                    recipiantIsStudent: true,
                ));

        // mail firme
        Mail::to($internship->company->contactPerson->email)
            ->sendNow(new InternshipStatusUpdated(
                    $internship,
                    $currentStatus,
                    $newStatus->status,
                    $request->note,
                    $user,
                    recipiantIsStudent: false,
                ));

        $newStatus->save();
        return response()->noContent();
    }

    public function index(Request $request)
    {
        $results = InternshipController::filterSearch($request);
        return response()->json($results);
    }
}
