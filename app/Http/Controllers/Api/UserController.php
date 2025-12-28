<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->is_admin ?? false, 403);

        $search = trim((string)$request->query('search', ''));

        $q = User::query()->select(['id', 'name', 'email'])->orderBy('id', 'desc');

        if ($search !== '') {
            $q->where(function ($qq) use ($search) {
                $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'data' => $q->limit(50)->get(),
        ]);
    }
}
