<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ActivityLogger
{
    public static function log(string $action, array $data, int $userId)
    {
        $activity = [
            'id' => uniqid(),
            'action' => $action,
            'data' => $data,
            'user_id' => $userId,
            'timestamp' => now()->toISOString(),
        ];

        $activities = Cache::get('activities', []);
        array_unshift($activities, $activity);
        $activities = array_slice($activities, 0, 1000); // Keep only last 1000
        Cache::put('activities', $activities, 3600); // 1 hour
    }

    public static function getRecentActivities(int $limit = 50)
    {
        $activities = Cache::get('activities', []);
        return array_slice($activities, 0, $limit);
    }

    public static function getUserActivities(int $userId, int $limit = 20)
    {
        $allActivities = self::getRecentActivities(200);
        
        $userActivities = array_filter($allActivities, function ($activity) use ($userId) {
            return isset($activity['user_id']) && $activity['user_id'] === $userId;
        });

        return array_slice($userActivities, 0, $limit);
    }
}