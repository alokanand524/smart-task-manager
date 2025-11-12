<?php

namespace App\Repositories\Interfaces;

interface TaskRepositoryInterface extends BaseRepositoryInterface
{
    public function getTasksByProject($projectId);
    public function getTasksByUser($userId);
    public function getOverdueTasks();
    public function searchTasks($query);
    public function getTasksWithFilters(array $filters);
}