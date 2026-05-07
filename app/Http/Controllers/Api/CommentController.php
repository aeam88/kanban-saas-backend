<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Comment;
use App\Models\Task;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'content' => $data['content'],
        ]);

        return response()->json($comment->load('user'), 201);
    }
}
