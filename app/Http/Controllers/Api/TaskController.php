<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Task;
use App\Models\Board;

class TaskController extends Controller
{
    public function index(Board $board)
    {
        $this->authorize('view', $board);
        return response()->json($board->tasks()->orderBy('position')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'workspace_id' => 'required|exists:workspaces,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string|in:low,medium,high',
        ]);

        $board = Board::findOrFail($data['board_id']);
        $this->authorize('update', $board);

        $lastPosition = $board->tasks()->max('position') ?? 0;
        $data['position'] = $lastPosition + 1000;

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string|in:low,medium,high', 
            'status' => 'nullable|string',
        ]);

        $task->update($data);

        return response()->json($task);
    }

    public function move(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $data = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'position' => 'required|numeric',
        ]);

        $newBoard = Board::findOrFail($data['board_id']);
        $this->authorize('view', $newBoard);

        $task->update([
            'board_id' => $data['board_id'],
            'position' => $data['position'],
        ]);

        event(new \App\Events\TaskMoved($task->load('board.project')));

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
