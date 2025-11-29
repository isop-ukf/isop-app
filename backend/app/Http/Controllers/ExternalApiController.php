<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;

class ExternalApiController extends Controller
{
    public function all_keys(Request $request)
    {
        $tokens = Sanctum::$personalAccessTokenModel::with('tokenable')->get();

        $tokens = $tokens->map(fn($token) => [
            "id" => $token->id,
            "name" => $token->name,
            "created_at" => $token->created_at,
            "last_used_at" => $token->last_used_at,
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

    public function update_internship_status(int $id)
    {
        // TODO: Implement in SCRUM-65
    }
}
