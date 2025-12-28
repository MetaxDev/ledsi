<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class TaskController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $tasks = $request->user()
            ->tasks()
            ->latest()
            ->get(['id', 'title', 'status', 'created_at', 'updated_at']);

        return response()->json([
            'data' => $tasks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStoreRequest $request): JsonResponse
    {
        $task = $request->user()->tasks()->create($request->validated());

        return response()->json([
            'data' => $task->only(['id', 'title', 'status', 'created_at', 'updated_at']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'data' => $task->only(['id', 'title', 'status', 'created_at', 'updated_at']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskUpdateRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return response()->json([
            'data' => $task->only(['id', 'title', 'status', 'created_at', 'updated_at']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([], 204);
    }
}
