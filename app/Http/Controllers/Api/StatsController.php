<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $authUser = $request->user();
        $requestedUserId = $request->integer('user_id');

        if (!($authUser->is_admin ?? false)) {
            $requestedUserId = $authUser->id;
        }

        $cacheKey = $requestedUserId
            ? "stats:user:{$requestedUserId}"
            : "stats:all";

        $payload = Cache::remember($cacheKey, 60, function () use ($requestedUserId) {
            $q = Task::query();

            if ($requestedUserId) {
                $q->where('user_id', $requestedUserId);
            }

            $total = (clone $q)->count();

            $rows = (clone $q)
                ->selectRaw('status, COUNT(*) as cnt')
                ->groupBy('status')
                ->pluck('cnt', 'status');

            return [
                'total' => $total,
                'by_status' => [
                    '0' => (int)($rows[0] ?? 0),
                    '1' => (int)($rows[1] ?? 0),
                    '2' => (int)($rows[2] ?? 0),
                ],
            ];
        });

        return response()->json(['data' => $payload]);
    }
}
