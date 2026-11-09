<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Replace the previous sample tasks without removing unrelated records.
            Task::whereIn('title', [
                'Review Laravel migrations',
                'Prepare database schema',
                'Review class notes',
                'Complete Laravel assessment',
                'Test the task list page',
                'Update project documentation',
                'Prepare project demonstration',
                'Review submission requirements',
            ])->delete();

            $tasks = [
                ['learning laravel', 'ongoing', '2026-11-01'],
                ['understanding vscode', 'ongoing', '2026-11-02'],
                ['review for summative', 'finished', '2026-11-06'],
                ['do laravel technicals', 'ongoing', '2026-11-05'],
                ['finish summative', 'finished', '2026-11-07'],
                ['update myproj for next technical', 'pending', '2026-11-08'],
                ['do technical 7', 'pending', '2026-11-09'],
                ['submit technical 7', 'pending', '2026-11-09'],
            ];

            foreach ($tasks as [$title, $status, $date]) {
                Task::updateOrCreate(['title' => $title], [
                    'status' => $status,
                    'task_date' => $date,
                ]);
            }
        });
    }
}
