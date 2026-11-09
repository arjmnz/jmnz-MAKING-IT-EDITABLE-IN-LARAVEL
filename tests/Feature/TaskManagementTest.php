<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // 00:30 in Manila is still the preceding calendar day in UTC.
        Carbon::setTestNow(Carbon::create(2026, 11, 9, 0, 30, 0, 'Asia/Manila'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_seeder_creates_eight_tasks_on_seven_dates_and_one_user(): void
    {
        $this->seed();
        $this->assertDatabaseCount('tasks', 8);
        $this->assertDatabaseCount('users', 1);
        $this->assertCount(7, Task::all()->pluck('task_date')->map->toDateString()->unique());
        $this->assertSame(2, Task::whereDate('task_date', '2026-11-09')->count());
    }

    public function test_welcome_displays_only_today_in_the_application_timezone(): void
    {
        $this->seed();
        $response = $this->get('/')->assertOk()->assertSeeText('November 9, 2026');
        foreach (Task::all() as $task) {
            if ($task->task_date->toDateString() === '2026-11-09') {
                $response->assertSeeText($task->title);
            } else {
                $response->assertDontSeeText($task->title);
            }
        }
        $response->assertViewHas('tasks', fn ($tasks) => $tasks->count() === 2);
    }

    public function test_task_list_shows_all_tasks_in_date_order(): void
    {
        Task::create(['title' => 'Future task', 'task_date' => '2026-10-30']);
        Task::create(['title' => 'Past task', 'task_date' => '2026-10-28']);
        Task::create(['title' => 'Current task', 'task_date' => '2026-10-29']);
        $this->get('/tasks')->assertOk()
            ->assertSeeInOrder(['Past task', 'Current task', 'Future task'])
            ->assertViewHas('tasks', fn ($tasks) => $tasks->count() === 3);
    }

    public function test_profile_displays_the_database_user(): void
    {
        User::create(['username' => 'teststudent', 'full_name' => 'Database Student', 'email' => 'student@example.com']);
        $this->get('/profile')->assertOk()->assertSeeText('teststudent')
            ->assertSeeText('Database Student')->assertSeeText('student@example.com');
    }

    public function test_about_identifies_the_developer(): void
    {
        $this->get('/about')->assertOk()->assertSeeText('Arwind Craig R. Jimenez');
    }

    public function test_empty_database_pages_are_handled(): void
    {
        $this->get('/')->assertOk()->assertSeeText('No tasks scheduled for today.');
        $this->get('/tasks')->assertOk()->assertSeeText('No tasks found.');
        $this->get('/profile')->assertOk()->assertSeeText('No profile found.');
    }

    public function test_database_defaults_task_status_to_pending(): void
    {
        $task = Task::create(['title' => 'Default status test', 'task_date' => today()->toDateString()]);
        $this->assertSame('pending', $task->fresh()->status);
    }

    public function test_username_must_be_unique(): void
    {
        User::factory()->create(['username' => 'sameuser']);
        $this->expectException(QueryException::class);
        User::factory()->create(['username' => 'sameuser']);
    }

    public function test_reseeding_preserves_created_users_and_eight_sample_tasks(): void
    {
        User::factory()->count(5)->create();
        $this->seed();
        $this->seed();
        $this->assertDatabaseCount('users', 6);
        $this->assertDatabaseCount('tasks', 8);
        Carbon::setTestNow(Carbon::create(2026, 11, 10, 12, 0, 0, 'Asia/Manila'));
        $this->seed();
        $this->assertDatabaseCount('tasks', 8);
        $this->assertSame(2, Task::whereDate('task_date', '2026-11-09')->count());
    }

    public function test_task_titles_are_escaped_in_both_views(): void
    {
        Task::create(['title' => '<script>alert("test")</script>', 'task_date' => today()->toDateString()]);
        foreach (['/', '/tasks'] as $url) {
            $this->get($url)->assertOk()->assertSee('&lt;script&gt;', false)
                ->assertDontSee('<script>', false);
        }
    }
}
