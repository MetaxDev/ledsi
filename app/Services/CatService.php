<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
class CatService
{
    private const TTL = 300;

    public function getFor(User $user): array
    {
        return Cache::remember($this->cacheKey($user->id), self::TTL, function () use ($user) {

            $resp = Http::timeout(3)->acceptJson()->get('https://catfact.ninja/fact');

            if (!$resp->ok()) {
                return [
                    'source' => 'fallback',
                    'fact' => "Кот пользователя #{$user->id} явно что-то замышляет.",
                ];
            }

            $json = $resp->json();

            return [
                'source' => 'catfact.ninja',
                'fact' => (string)($json['fact'] ?? 'Факт не найден.'),
            ];
        });
    }

    public function warmFor(User $user): void
    {
        $this->getFor($user);
    }

    public function forgetFor(int $userId): void
    {
        Cache::forget($this->cacheKey($userId));
    }

    private function cacheKey(int $userId): string
    {
        return "catfact:user:{$userId}";
    }
}
