<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskObserver
{
    public function created(Task $task): void
    {
        $this->forgetStats($task);
    }

    public function updated(Task $task): void
    {
        logger()->info('TaskObserver@updated', [
            'task_id' => $task->id,
            'user_id' => $task->user_id,
        ]);

        $this->forgetStats($task);
    }

    public function deleted(Task $task): void
    {
        $this->forgetStats($task);
    }

    private function forgetStats(Task $task): void
    {
        Cache::forget("stats:user:{$task->user_id}");
        Cache::forget("stats:all");
    }
}
