<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatController extends Controller
{
    public function __construct(protected CatService $catService) {}

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => $this->catService->getFor($user),
        ]);
    }
}
