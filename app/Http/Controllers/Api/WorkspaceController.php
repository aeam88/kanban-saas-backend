<?php

namespace App\Http\Controllers\Api;




use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workspace;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WorkspaceController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        
        $workspaces = Workspace::where('owner_id', $userId)
            ->orWhereHas('users', function($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->withCount(['projects', 'users as members_count'])
            ->get();

        return response()->json($workspaces);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $workspace = Workspace::create([
            'name' => $data['name'],
            'owner_id' => $request->user()->id,
        ]);

        $workspace->users()->attach($request->user()->id, ['role' => 'admin']);
        $workspace->loadCount(['projects', 'users as members_count']);

        return response()->json($workspace, 201);
    }

    public function show(Workspace $workspace)
    {
        $this->authorize('view', $workspace);
        
        $workspace->load(['users', 'projects']);
        $workspace->loadCount(['projects', 'users as members_count']);
        
        return response()->json($workspace);
    }

    public function invite(Request $request, Workspace $workspace)
    {
        $this->authorize('invite', $workspace);

        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:admin,member',
        ]);

        $userToInvite = \App\Models\User::where('email', $data['email'])->first();

        if ($workspace->users->contains($userToInvite)) {
            return response()->json(['message' => 'User already in workspace'], 422);
        }

        $workspace->users()->attach($userToInvite->id, ['role' => $data['role']]);

        return response()->json(['message' => 'User invited successfully']);
    }

    public function destroy(Workspace $workspace)
    {
        $this->authorize('delete', $workspace);
        
        $workspace->delete();

        return response()->json(['message' => 'Workspace deleted successfully']);
    }
}
