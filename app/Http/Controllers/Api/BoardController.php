<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Board;
use App\Models\Project;

class BoardController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);
        return response()->json($project->boards()->orderBy('position')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
        ]);

        $project = Project::findOrFail($data['project_id']);
        $this->authorize('update', $project);

        $lastPosition = $project->boards()->max('position') ?? 0;
        $data['position'] = $lastPosition + 1000;

        $board = Board::create($data);

        return response()->json($board, 201);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'ids' => 'required|array',
            'ids.*' => 'exists:boards,id',
        ]);

        $project = Project::findOrFail($data['project_id']);
        $this->authorize('update', $project);

        foreach ($data['ids'] as $index => $id) {
            Board::where('id', $id)->update(['position' => ($index + 1) * 1000]);
        }

        return response()->json(['message' => 'Boards reordered']);
    }
}
