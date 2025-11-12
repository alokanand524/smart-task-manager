<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public static function remember(string $key, int $ttl, callable $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public static function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    public static function flush(): bool
    {
        return Cache::flush();
    }

    public static function userTasks(int $userId): string
    {
        return "user_tasks_{$userId}";
    }

    public static function projectTasks(int $projectId): string
    {
        return "project_tasks_{$projectId}";
    }

    public static function dashboardStats(int $userId): string
    {
        return "dashboard_stats_{$userId}";
    }
}