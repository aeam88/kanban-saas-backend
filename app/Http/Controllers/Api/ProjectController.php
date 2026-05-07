<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Workspace;

class ProjectController extends Controller
{
    public function index(Workspace $workspace)
    {
        $this->authorize('view', $workspace);
        return response()->json($workspace->projects);
    }

    public function board(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['workspace']);
        
        $columns = $project->boards()
            ->with(['tasks' => function($query) {
                $query->orderBy('position');
                $query->with(['assignee']);
            }])
            ->orderBy('position')
            ->get();

        return response()->json([
            'project' => $project,
            'columns' => $columns
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'workspace_id' => 'required|exists:workspaces,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $workspace = Workspace::findOrFail($data['workspace_id']);
        $this->authorize('update', $workspace);

        $project = Project::create($data);

        return response()->json($project, 201);
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($data);

        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return response()->json(['message' => 'Project deleted']);
    }
}
