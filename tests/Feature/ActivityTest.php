<?php
//
//use App\Constants\Routes;
//use App\Enums\V1\MembershipRoleEnum;
//use App\Enums\V1\TaskStatusEnum;
//use App\Models\V1\ActivityLog;
//use App\Models\V1\Organization;
//use App\Models\V1\Project;
//use App\Models\V1\Task;
//use App\Models\V1\User;
//use Illuminate\Foundation\Testing\RefreshDatabase;
//
//uses(RefreshDatabase::class);
//
//beforeEach(function () {
//    $this->user = User::factory()->create();
//    $this->viewer = User::factory()->create();
//    $this->outsider = User::factory()->create();
//
//    $this->organization = Organization::factory()->create();
//
//    $this->organization->members()->attach($this->user->id, [
//        'role' => MembershipRoleEnum::ADMIN->value,
//        'joined_at' => now(),
//    ]);
//
//    $this->organization->members()->attach($this->viewer->id, [
//        'role' => MembershipRoleEnum::VIEWER->value,
//        'joined_at' => now(),
//    ]);
//
//    $this->project = Project::factory()->create([
//        'organization_id' => $this->organization->id,
//        'key' => 'TEST',
//    ]);
//
//    $this->task = Task::factory()->create([
//        'project_id' => $this->project->id,
//        'reporter_id' => $this->user->id,
//        'reference_number' => 1,
//        'reference' => 'TEST-1',
//        'status' => TaskStatusEnum::BACKLOG->value,
//    ]);
//
//    $this->payload = [
//        'title' => 'Activity test task',
//        'description' => 'Testing activity log',
//        'type' => 'task',
//        'status' => 'backlog',
//        'priority' => 'medium',
//    ];
//});
//
//// ── Observer — Task Created ───────────────────────────────────────────────────
//
//describe('observer — task created', function () {
//
//    it('logs activity when a task is created', function () {
//        $this->actingAs($this->user);
//
//        $this->postJson(route(
//            Routes::STORE_TASK,
//            [$this->organization->uuid, $this->project->uuid]
//        ), $this->payload)->assertCreated();
//
//        $this->assertDatabaseHas('activity_logs', [
//            'loggable_type' => Task::class,
//            'event' => 'created',
//            'user_id' => $this->user->id,
//        ]);
//    });
//
//    it('stores null old_values on task creation', function () {
//        $this->actingAs($this->user);
//
//        $this->postJson(route(
//            Routes::STORE_TASK,
//            [$this->organization->uuid, $this->project->uuid]
//        ), $this->payload);
//
//        $log = ActivityLog::where('event', 'created')
//            ->where('loggable_type', Task::class)
//            ->latest()
//            ->first();
//
//        expect($log->old_values)->toBeNull();
//    });
//
//    it('stores correct new_values on task creation', function () {
//        $this->actingAs($this->user);
//
//        $response = $this->postJson(route(
//            Routes::STORE_TASK,
//            [$this->organization->uuid, $this->project->uuid]
//        ), $this->payload);
//
//        $task = Task::where('uuid', $response->json('data.id'))->first();
//        $log = ActivityLog::where('event', 'created')
//            ->where('loggable_id', $task->id)
//            ->first();
//
//        expect($log->new_values)->toHaveKey('title');
//        expect($log->new_values['title'])->toBe($this->payload['title']);
//    });
//
//    it('does not expose internal fields in new_values on creation', function () {
//        $this->actingAs($this->user);
//
//        $this->postJson(route(
//            Routes::STORE_TASK,
//            [$this->organization->uuid, $this->project->uuid]
//        ), $this->payload);
//
//        $log = ActivityLog::where('event', 'created')
//            ->where('loggable_type', Task::class)
//            ->latest()
//            ->first();
//
//        expect($log->new_values)->not->toHaveKey('id');
//        expect($log->new_values)->not->toHaveKey('project_id');
//        expect($log->new_values)->not->toHaveKey('reporter_id');
//        expect($log->new_values)->not->toHaveKey('reference_number');
//    });
//
//    it('stores the correct user_id on task creation', function () {
//        $this->actingAs($this->user);
//
//        $response = $this->postJson(route(
//            Routes::STORE_TASK,
//            [$this->organization->uuid, $this->project->uuid]
//        ), $this->payload);
//
//        $taskUuid = $response->json('data.id');
//
//        $task = Task::where('uuid', $taskUuid)->first();
//
//        $log = ActivityLog::where('event', 'created')
//            ->where('loggable_type', Task::class)
//            ->where('loggable_id', $task->id)  // scope to the specific task
//            ->first();
//
//        expect($log->user_id)->toBe($this->user->id);
//    });
//});
//
//// ── Observer — Task Updated ───────────────────────────────────────────────────
//
//describe('observer — task updated', function () {
//
//    it('logs activity when a task is updated', function () {
//        $this->actingAs($this->user);
//
//        $this->patchJson(route(
//            Routes::UPDATE_TASK,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ), ['title' => 'Updated title']);
//
//        $this->assertDatabaseHas('activity_logs', [
//            'loggable_type' => Task::class,
//            'loggable_id' => $this->task->id,
//            'event' => 'updated',
//            'user_id' => $this->user->id,
//        ]);
//    });
//
//    it('stores only changed fields in new_values on update', function () {
//        $this->actingAs($this->user);
//
//        $this->patchJson(route(
//            Routes::UPDATE_TASK,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ), ['title' => 'Updated title']);
//
//        $log = ActivityLog::where('event', 'updated')
//            ->where('loggable_id', $this->task->id)
//            ->latest()
//            ->first();
//
//        expect($log->new_values)->toHaveKey('title');
//        expect($log->new_values['title'])->toBe('Updated title');
//        expect($log->new_values)->not->toHaveKey('status'); // unchanged — should not appear
//    });
//
//    it('stores original values in old_values on update', function () {
//        $originalTitle = $this->task->title;
//        $this->actingAs($this->user);
//
//        $this->patchJson(route(
//            Routes::UPDATE_TASK,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ), ['title' => 'Updated title']);
//
//        $log = ActivityLog::where('event', 'updated')
//            ->where('loggable_id', $this->task->id)
//            ->latest()
//            ->first();
//
//        expect($log->old_values['title'])->toBe($originalTitle);
//    });
//
//    it('does not log activity when only updated_at changes', function () {
//        $count = ActivityLog::where('loggable_id', $this->task->id)->count();
//
//        $this->task->touch();
//
//        expect(ActivityLog::where('loggable_id', $this->task->id)->count())->toBe($count);
//    });
//
//    it('logs activity when task status is updated', function () {
//        $this->actingAs($this->user);
//
//        $this->patchJson(route(
//            Routes::UPDATE_TASK_STATUS,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ), ['status' => 'in_progress']);
//
//        $this->assertDatabaseHas('activity_logs', [
//            'loggable_type' => Task::class,
//            'loggable_id' => $this->task->id,
//            'event' => 'updated',
//        ]);
//    });
//
//    it('logs completed_at when status changes to done', function () {
//        $this->actingAs($this->user);
//
//        $this->patchJson(route(
//            Routes::UPDATE_TASK_STATUS,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ), ['status' => 'done']);
//
//        $log = ActivityLog::where('loggable_id', $this->task->id)
//            ->where('event', 'updated')
//            ->latest()
//            ->first();
//
//        expect($log->new_values)->toHaveKey('completed_at');
//    });
//});
//
//// ── Observer — Task Deleted ───────────────────────────────────────────────────
//
//describe('observer — task deleted', function () {
//
//    it('logs activity when a task is deleted', function () {
//        $this->actingAs($this->user);
//
//        $this->deleteJson(route(
//            Routes::DESTROY_TASK,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ));
//
//        $this->assertDatabaseHas('activity_logs', [
//            'loggable_type' => Task::class,
//            'loggable_id' => $this->task->id,
//            'event' => 'deleted',
//            'user_id' => $this->user->id,
//        ]);
//    });
//
//    it('stores original values in old_values on delete', function () {
//        $this->actingAs($this->user);
//
//        $this->deleteJson(route(
//            Routes::DESTROY_TASK,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ));
//
//        $log = ActivityLog::where('event', 'deleted')
//            ->where('loggable_id', $this->task->id)
//            ->latest()
//            ->first();
//
//        expect($log->old_values)->not->toBeNull();
//        expect($log->old_values['title'])->toBe($this->task->title);
//        expect($log->new_values)->toBeNull();
//    });
//
//    it('does not expose internal fields in old_values on delete', function () {
//        $this->actingAs($this->user);
//
//        $this->deleteJson(route(
//            Routes::DESTROY_TASK,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ));
//
//        $log = ActivityLog::where('event', 'deleted')
//            ->where('loggable_id', $this->task->id)
//            ->latest()
//            ->first();
//
//        expect($log->old_values)->not->toHaveKey('id');
//        expect($log->old_values)->not->toHaveKey('project_id');
//        expect($log->old_values)->not->toHaveKey('reporter_id');
//        expect($log->old_values)->not->toHaveKey('reference_number');
//    });
//});
//
//// ── GET /tasks/{task}/activity ────────────────────────────────────────────────
//
//describe('GET /tasks/{task}/activity — index', function () {
//
//    it('returns activity log for a task', function () {
//        $this->actingAs($this->user);
//
//        $this->patchJson(route(
//            Routes::UPDATE_TASK,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ), ['title' => 'Trigger activity']);
//
//        $this->getJson(route(
//            Routes::INDEX_TASK_ACTIVITY,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ))->assertOk()
//            ->assertJsonStructure([
//                'data' => [
//                    'items' => [
//                        '*' => ['event', 'user', 'oldValues', 'newValues', 'createdAt']
//                    ]
//                ]
//            ]);
//    });
//
//    it('returns activity in descending order', function () {
//        $this->actingAs($this->user);
//
//        $this->patchJson(route(
//            Routes::UPDATE_TASK,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ), ['title' => 'First update']);
//
//        $this->patchJson(route(
//            Routes::UPDATE_TASK,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ), ['title' => 'Second update']);
//
//        $response = $this->getJson(route(
//            Routes::INDEX_TASK_ACTIVITY,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ))->assertOk();
//
//        $items = $response->json('data.items');
//        $titles = collect($items)->pluck('newValues.title')->filter()->values();
//
//        expect($titles)->toContain('First update');
//        expect($titles)->toContain('Second update');
//    });
//
//    it('allows viewer to see activity', function () {
//        $this->actingAs($this->viewer);
//
//        $this->getJson(route(
//            Routes::INDEX_TASK_ACTIVITY,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ))->assertOk();
//    });
//
//    it('returns activity for soft deleted task', function () {
//        $this->actingAs($this->user);
//
//        $this->deleteJson(route(
//            Routes::DESTROY_TASK,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ));
//
//        $this->getJson(route(
//            Routes::INDEX_TASK_ACTIVITY,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ))->assertOk();
//    });
//
//    it('returns paginated results', function () {
//        $this->actingAs($this->user);
//
//        $response = $this->getJson(route(
//            Routes::INDEX_TASK_ACTIVITY,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ))->assertOk();
//
//        $response->assertJsonStructure([
//            'data' => [
//                'pagination' => [
//                    'meta' => ['currentPage', 'lastPage', 'perPage', 'total'],
//                ]
//            ]
//        ]);
//    });
//
//    it('fails if user is not an org member', function () {
//        $this->actingAs($this->outsider);
//
//        $this->getJson(route(
//            Routes::INDEX_TASK_ACTIVITY,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ))->assertForbidden();
//    });
//
//    it('fails if unauthenticated', function () {
//        $this->getJson(route(
//            Routes::INDEX_TASK_ACTIVITY,
//            [$this->organization->uuid, $this->project->uuid, $this->task->uuid]
//        ))->assertUnauthorized();
//    });
//
//    it('returns 404 for non-existent task', function () {
//        $this->actingAs($this->user);
//
//        $this->getJson(route(
//            Routes::INDEX_TASK_ACTIVITY,
//            [$this->organization->uuid, $this->project->uuid, fake()->uuid()]
//        ))->assertNotFound();
//    });
//});
