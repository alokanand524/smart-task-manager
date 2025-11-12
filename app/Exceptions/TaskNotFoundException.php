<?php

namespace App\Exceptions;

use Exception;

class TaskNotFoundException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'error' => 'Task not found',
            'message' => 'The requested task does not exist or you do not have permission to access it.'
        ], 404);
    }
}