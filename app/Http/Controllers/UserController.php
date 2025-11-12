<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('role')
                    ->when($request->role, function ($query) use ($request) {
                        return $query->whereHas('role', function ($q) use ($request) {
                            $q->where('name', $request->role);
                        });
                    })
                    ->when($request->search, function ($query) use ($request) {
                        return $query->where('name', 'like', '%' . $request->search . '%')
                                    ->orWhere('email', 'like', '%' . $request->search . '%');
                    })
                    ->paginate(15);

        return response()->json($users);
    }

    public function show(User $user)
    {
        return response()->json($user->load(['role', 'projects', 'assignedTasks.project']));
    }

    public function updateRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update(['role_id' => $request->role_id]);

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user->load('role')
        ]);
    }
}