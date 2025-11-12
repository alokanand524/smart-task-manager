<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::with(['creator', 'tasks', 'members'])
            ->when($request->user()->role->name === 'Employee', function ($query) use ($request) {
                return $query->whereHas('members', function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                })->orWhere('created_by', $request->user()->id);
            })
            ->paginate(10);

        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($project->load(['creator', 'tasks', 'members']), 201);
    }

    public function show(Project $project)
    {
        return response()->json($project->load(['creator', 'tasks.assignees', 'members']));
    }

    public function update(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $project->update($request->only(['name', 'description']));

        return response()->json($project->load(['creator', 'tasks', 'members']));
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully']);
    }

    public function addMember(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $project->members()->syncWithoutDetaching([$request->user_id]);

        return response()->json(['message' => 'Member added successfully']);
    }

    public function removeMember(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $project->members()->detach($request->user_id);

        return response()->json(['message' => 'Member removed successfully']);
    }
}