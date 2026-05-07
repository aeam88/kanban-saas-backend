<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Project;
use App\Models\Board;
use App\Models\Task;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        $users = User::factory(5)->create();
        $allUsers = $users->push($admin);

        Workspace::factory(2)
            ->create(['owner_id' => $admin->id])
            ->each(function ($workspace) use ($admin, $allUsers) {
                $workspace->users()->attach($admin->id, ['role' => 'admin']);
                
                $randomUsers = $allUsers->random(rand(2, 4))->pluck('id');
                foreach ($randomUsers as $userId) {
                    if ($userId !== $admin->id) {
                        $workspace->users()->attach($userId, ['role' => 'member']);
                    }
                }

                Project::factory(rand(2, 3))
                    ->create(['workspace_id' => $workspace->id])
                    ->each(function ($project) use ($workspace) {
                        $boardNames = ['To Do', 'Doing', 'Done'];
                        foreach ($boardNames as $index => $name) {
                            $board = Board::factory()->create([
                                'project_id' => $project->id,
                                'name' => $name,
                                'position' => $index + 1,
                            ]);

                            Task::factory(rand(3, 8))->create([
                                'board_id' => $board->id,
                                'workspace_id' => $workspace->id,
                            ]);
                        }
                    });
            });

        Workspace::factory(3)->create()->each(function ($workspace) {
            Project::factory(1)->create(['workspace_id' => $workspace->id])->each(function ($project) use ($workspace) {
                Board::factory(3)->create(['project_id' => $project->id])->each(function ($board) use ($workspace) {
                    Task::factory(2)->create([
                        'board_id' => $board->id,
                        'workspace_id' => $workspace->id,
                    ]);
                });
            });
        });
    }
}
