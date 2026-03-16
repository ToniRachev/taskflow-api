<?php

use App\Constants\Routes;
use App\Enums\V1\MembershipRoleEnum;
use App\Enums\V1\TaskStatusEnum;
use App\Models\V1\Organization;
use App\Models\V1\Project;
use App\Models\V1\Task;
use App\Models\V1\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->assignee = User::factory()->create();
    $this->viewer = User::factory()->create();
    $this->outsider = User::factory()->create();

    $this->organization = Organization::factory()->create();

    $this->organization->members()->attach($this->user->id, [
        'role' => MembershipRoleEnum::ADMIN->value,
        'joined_at' => now(),
    ]);

    $this->organization->members()->attach($this->assignee->id, [
        'role' => MembershipRoleEnum::MEMBER->value,
        'joined_at' => now(),
    ]);

    $this->organization->members()->attach($this->viewer->id, [
        'role' => MembershipRoleEnum::VIEWER->value,
        'joined_at' => now(),
    ]);

    $this->project = Project::factory()->create([
        'organization_id' => $this->organization->id,
        'key' => 'TEST',
    ]);

    $this->task = Task::factory()->create([
        'project_id' => $this->project->id,
        'reporter_id' => $this->user->id,
        'assignee_id' => $this->assignee->id,
        'reference_number' => 1,
        'reference' => 'TEST-1',
        'status' => TaskStatusEnum::BACKLOG->value,
    ]);

    $this->payload = [
        'title' => 'Task test title',
        'description' => 'Task test description',
        'type' => 'task',
        'status' => 'backlog',
        'priority' => 'medium',
        'storyPoints' => 5,
        'dueDate' => '2026-10-10',
    ];
});

// ── Store Task ────────────────────────────────────────────────────────────────

describe('POST /tasks — store', function () {

    it('successfully creates a task', function () {
        $this->actingAs($this->user);

        $response = $this->postJson(route(
            Routes::STORE_TASK,
            [$this->project->uuid]
        ), $this->payload)->assertCreated();

        $this->assertDatabaseHas('tasks', [
            'uuid' => $response->json('data.id'),
            'project_id' => $this->project->id,
            'title' => $this->payload['title'],
        ]);
    });

    it('generates correct reference on first task', function () {
        $this->actingAs($this->user);

        $this->postJson(route(
            Routes::STORE_TASK,
            [$this->project->uuid]
        ), $this->payload)
            ->assertJsonPath('data.reference', 'TEST-2'); // task fixture already used TEST-1
    });

    it('increments reference number on each creation', function () {
        $this->actingAs($this->user);

        $first = $this->postJson(route(
            Routes::STORE_TASK,
            [$this->project->uuid]
        ), $this->payload)->json('data.reference');

        $second = $this->postJson(route(
            Routes::STORE_TASK,
            [$this->project->uuid]
        ), $this->payload)->json('data.reference');

        expect($first)->toBe('TEST-2');
        expect($second)->toBe('TEST-3');
    });

    it('creates a task with an assignee', function () {
        $this->actingAs($this->user);

        $response = $this->postJson(route(
            Routes::STORE_TASK,
            [$this->project->uuid]
        ), [...$this->payload, 'assigneeId' => $this->assignee->uuid])
            ->assertCreated();

        $this->assertDatabaseHas('tasks', [
            'uuid' => $response->json('data.id'),
            'assignee_id' => $this->assignee->id,
        ]);
    });

    it('creates a task without an assignee', function () {
        $this->actingAs($this->user);

        $response = $this->postJson(route(
            Routes::STORE_TASK,
            [$this->project->uuid]
        ), $this->payload)->assertCreated();

        expect($response->json('data.assignee'))->toBeNull();
    });

    it('fails if user is not an org member', function () {
        $this->actingAs($this->outsider);

        $this->postJson(route(
            Routes::STORE_TASK,
            [$this->project->uuid]
        ), $this->payload)->assertForbidden();
    });

    it('fails if viewer tries to create a task', function () {
        $this->actingAs($this->viewer);

        $this->postJson(route(
            Routes::STORE_TASK,
            [$this->project->uuid]
        ), $this->payload)->assertForbidden();
    });

    it('fails if unauthenticated', function () {
        $this->postJson(route(
            Routes::STORE_TASK,
            [$this->project->uuid]
        ), $this->payload)->assertUnauthorized();
    });

    it('fails if title is missing', function () {
        $this->actingAs($this->user);

        $this->postJson(route(
            Routes::STORE_TASK,
            [$this->project->uuid]
        ), [...$this->payload, 'title' => ''])->assertUnprocessable();
    });

    it('fails if type is invalid', function () {
        $this->actingAs($this->user);

        $this->postJson(route(
            Routes::STORE_TASK,
            [$this->project->uuid]
        ), [...$this->payload, 'type' => 'invalid'])->assertUnprocessable();
    });

    it('fails if assignee uuid does not exist', function () {
        $this->actingAs($this->user);

        $this->postJson(route(
            Routes::STORE_TASK,
            [$this->project->uuid]
        ), [...$this->payload, 'assigneeId' => fake()->uuid()])->assertUnprocessable();
    });
});

// ── Index Tasks ───────────────────────────────────────────────────────────────

describe('GET /tasks — index', function () {

    it('returns paginated task list', function () {
        $this->actingAs($this->user);

        $this->getJson(route(
            Routes::INDEX_TASK,
            [$this->project->uuid]
        ))->assertOk()->assertJsonStructure([
            'success',
            'data' => [
                'items' => [[
                    'id', 'reference', 'title', 'status', 'priority'
                ]]
            ]
        ]);
    });

    it('filters tasks by status', function () {
        $this->actingAs($this->user);

        Task::factory()->create([
            'project_id' => $this->project->id,
            'reporter_id' => $this->user->id,
            'status' => TaskStatusEnum::DONE->value,
            'reference_number' => 2,
            'reference' => 'TEST-2',
        ]);

        $response = $this->getJson(route(
                Routes::INDEX_TASK,
                [$this->project->uuid]
            ) . '?status=done')->assertOk();

        collect($response->json('data.items'))->each(
            fn($task) => expect($task['status'])->toBe('done')
        );
    });

    it('filters tasks by priority', function () {
        $this->actingAs($this->user);

        $response = $this->getJson(route(
                Routes::INDEX_TASK,
                [$this->project->uuid]
            ) . '?priority=medium')->assertOk();

        collect($response->json('data.items'))->each(
            fn($task) => expect($task['priority'])->toBe('medium')
        );
    });

    it('filters tasks by assignee', function () {
        $this->actingAs($this->user);

        $response = $this->getJson(route(
                Routes::INDEX_TASK,
                [$this->project->uuid]
            ) . '?assignee=' . $this->assignee->uuid)->assertOk();

        collect($response->json('data.items'))->each(
            fn($task) => expect($task['assignee']['id'])->toBe($this->assignee->uuid)
        );
    });

    it('fails if unauthenticated', function () {
        $this->getJson(route(
            Routes::INDEX_TASK,
            [$this->project->uuid]
        ))->assertUnauthorized();
    });

    it('fails if user is not an org member', function () {
        $this->actingAs($this->outsider);

        $this->getJson(route(
            Routes::INDEX_TASK,
            [$this->project->uuid]
        ))->assertForbidden();
    });
});

// ── Show Task ─────────────────────────────────────────────────────────────────

describe('GET /tasks/{task} — show', function () {

    it('returns full task detail', function () {
        $this->actingAs($this->user);

        $this->getJson(route(
            Routes::SHOW_TASK,
            [$this->task->uuid]
        ))->assertOk()->assertJsonStructure([
            'data' => [
                'id', 'reference', 'title', 'status',
                'priority', 'type', 'reporter', 'assignee',
                'subtaskCount', 'commentCount', 'activity',
            ],
        ]);
    });

    it('allows viewer to view a task', function () {
        $this->actingAs($this->viewer);

        $this->getJson(route(
            Routes::SHOW_TASK,
            [$this->task->uuid]
        ))->assertOk();
    });

    it('fails if user is not an org member', function () {
        $this->actingAs($this->outsider);

        $this->getJson(route(
            Routes::SHOW_TASK,
            [$this->task->uuid]
        ))->assertForbidden();
    });

    it('fails if unauthenticated', function () {
        $this->getJson(route(
            Routes::SHOW_TASK,
            [$this->task->uuid]
        ))->assertUnauthorized();
    });

    it('returns 404 for non-existent task', function () {
        $this->actingAs($this->user);

        $this->getJson(route(
            Routes::SHOW_TASK,
            [fake()->uuid()]
        ))->assertNotFound();
    });
});

// ── Update Task ───────────────────────────────────────────────────────────────

describe('PATCH /tasks/{task} — update', function () {

    it('successfully updates a task', function () {
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::UPDATE_TASK,
            [$this->task->uuid]
        ), ['title' => 'Updated title'])->assertOk();

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'title' => 'Updated title',
        ]);
    });

    it('allows assignee to update their task', function () {
        $this->actingAs($this->assignee);

        $this->patchJson(route(
            Routes::UPDATE_TASK,
            [$this->task->uuid]
        ), ['title' => 'Assignee updated'])->assertOk();
    });

    it('fails if viewer tries to update', function () {
        $this->actingAs($this->viewer);

        $this->patchJson(route(
            Routes::UPDATE_TASK,
            [$this->task->uuid]
        ), ['title' => 'Viewer update'])->assertForbidden();
    });

    it('fails if member tries to update a task they are not involved in', function () {
        $member = User::factory()->create();
        $this->organization->members()->attach($member->id, [
            'role' => MembershipRoleEnum::MEMBER->value,
            'joined_at' => now(),
        ]);

        $this->actingAs($member);

        $this->patchJson(route(
            Routes::UPDATE_TASK,
            [$this->task->uuid]
        ), ['title' => 'Should fail'])->assertForbidden();
    });

    it('fails if title is sent as empty string', function () {
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::UPDATE_TASK,
            [$this->task->uuid]
        ), ['title' => ''])->assertUnprocessable();
    });

    it('clears due date when sent as null', function () {
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::UPDATE_TASK,
            [$this->task->uuid]
        ), ['dueDate' => null])->assertOk();

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'due_date' => null,
        ]);
    });

    it('fails if unauthenticated', function () {
        $this->patchJson(route(
            Routes::UPDATE_TASK,
            [$this->task->uuid]
        ), ['title' => 'Unauthorized'])->assertUnauthorized();
    });
});

// ── Delete Task ───────────────────────────────────────────────────────────────

describe('DELETE /tasks/{task} — destroy', function () {

    it('successfully soft deletes a task', function () {
        $this->actingAs($this->user);

        $this->deleteJson(route(
            Routes::DESTROY_TASK,
            [$this->task->uuid]
        ))->assertNoContent();

        $this->assertSoftDeleted('tasks', ['id' => $this->task->id]);
    });

    it('fails if viewer tries to delete', function () {
        $this->actingAs($this->viewer);

        $this->deleteJson(route(
            Routes::DESTROY_TASK,
            [$this->task->uuid]
        ))->assertForbidden();
    });

    it('fails if member tries to delete a task they did not report', function () {
        $this->actingAs($this->assignee); // assignee but not reporter

        $this->deleteJson(route(
            Routes::DESTROY_TASK,
            [$this->task->uuid]
        ))->assertForbidden();
    });

    it('fails if unauthenticated', function () {
        $this->deleteJson(route(
            Routes::DESTROY_TASK,
            [$this->task->uuid]
        ))->assertUnauthorized();
    });
});

// ── Update Status ─────────────────────────────────────────────────────────────

describe('PATCH /tasks/{task}/status — update status', function () {

    it('successfully updates task status', function () {
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::UPDATE_TASK_STATUS,
            [$this->task->uuid]
        ), ['status' => 'in_progress'])->assertOk();

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'status' => 'in_progress',
        ]);
    });

    it('sets completed_at when status changes to done', function () {
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::UPDATE_TASK_STATUS,
            [$this->task->uuid]
        ), ['status' => 'done'])->assertOk();

        $task = $this->task->fresh();
        expect($task->completed_at)->not->toBeNull();
    });

    it('clears completed_at when status changes away from done', function () {
        $this->task->update(['status' => 'done', 'completed_at' => now()]);
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::UPDATE_TASK_STATUS,
            [$this->task->uuid]
        ), ['status' => 'in_progress'])->assertOk();

        $task = $this->task->fresh();
        expect($task->completed_at)->toBeNull();
    });

    it('fails if status is invalid', function () {
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::UPDATE_TASK_STATUS,
            [$this->task->uuid]
        ), ['status' => 'invalid'])->assertUnprocessable();
    });

    it('fails if viewer tries to update status', function () {
        $this->actingAs($this->viewer);

        $this->patchJson(route(
            Routes::UPDATE_TASK_STATUS,
            [$this->task->uuid]
        ), ['status' => 'in_progress'])->assertForbidden();
    });
});

// ── Update Assignee ───────────────────────────────────────────────────────────

describe('PATCH /tasks/{task}/assign — update assignee', function () {

    it('successfully assigns a task', function () {
        $this->actingAs($this->user);

        $newAssignee = User::factory()->create();
        $this->organization->members()->attach($newAssignee->id, [
            'role' => MembershipRoleEnum::MEMBER->value,
            'joined_at' => now(),
        ]);

        $this->patchJson(route(
            Routes::UPDATE_TASK_ASSIGNEE,
            [$this->task->uuid]
        ), ['assigneeId' => $newAssignee->uuid])->assertOk();

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'assignee_id' => $newAssignee->id,
        ]);
    });

    it('unassigns a task when null is sent', function () {
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::UPDATE_TASK_ASSIGNEE,
            [$this->task->uuid]
        ), ['assigneeId' => null])->assertOk();

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'assignee_id' => null,
        ]);
    });

    it('fails if assignee tries to reassign the task', function () {
        $this->actingAs($this->assignee);

        $this->patchJson(route(
            Routes::UPDATE_TASK_ASSIGNEE,
            [$this->task->uuid]
        ), ['assigneeId' => $this->user->uuid])->assertForbidden();
    });

    it('fails if viewer tries to assign', function () {
        $this->actingAs($this->viewer);

        $this->patchJson(route(
            Routes::UPDATE_TASK_ASSIGNEE,
            [$this->task->uuid]
        ), ['assigneeId' => $this->assignee->uuid])->assertForbidden();
    });

    it('fails if assignee uuid does not exist', function () {
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::UPDATE_TASK_ASSIGNEE,
            [$this->task->uuid]
        ), ['assigneeId' => fake()->uuid()])->assertUnprocessable();
    });
});

// ── Update Priority ───────────────────────────────────────────────────────────

describe('PATCH /tasks/{task}/priority — update priority', function () {

    it('successfully updates priority', function () {
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::UPDATE_TASK_PRIORITY,
            [$this->task->uuid]
        ), ['priority' => 'high'])->assertOk();

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'priority' => 'high',
        ]);
    });

    it('fails if priority is invalid', function () {
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::UPDATE_TASK_PRIORITY,
            [$this->task->uuid]
        ), ['priority' => 'urgent'])->assertUnprocessable();
    });

    it('fails if viewer tries to update priority', function () {
        $this->actingAs($this->viewer);

        $this->patchJson(route(
            Routes::UPDATE_TASK_PRIORITY,
            [$this->task->uuid]
        ), ['priority' => 'high'])->assertForbidden();
    });
});

// ── Subtasks ──────────────────────────────────────────────────────────────────

describe('POST /tasks/{task}/subtasks — store subtask', function () {

    it('successfully creates a subtask', function () {
        $this->actingAs($this->user);

        $response = $this->postJson(route(
            Routes::STORE_SUBTASK,
            [$this->task->uuid]
        ), $this->payload)->assertCreated();

        $this->assertDatabaseHas('tasks', [
            'uuid' => $response->json('data.id'),
            'parent_id' => $this->task->id,
        ]);
    });

    it('subtask reference is scoped to the project', function () {
        $this->actingAs($this->user);

        $response = $this->postJson(route(
            Routes::STORE_SUBTASK,
            [$this->task->uuid]
        ), $this->payload)->assertCreated();

        expect($response->json('data.reference'))->toStartWith('TEST-');
    });

    it('fails if viewer tries to create a subtask', function () {
        $this->actingAs($this->viewer);

        $this->postJson(route(
            Routes::STORE_SUBTASK,
            [$this->task->uuid]
        ), $this->payload)->assertForbidden();
    });
});

describe('GET /tasks/{task}/subtasks — index subtasks', function () {

    it('returns subtasks of the task', function () {
        Task::factory()->create([
            'project_id' => $this->project->id,
            'reporter_id' => $this->user->id,
            'parent_id' => $this->task->id,
            'reference_number' => 2,
            'reference' => 'TEST-2',
        ]);

        $this->actingAs($this->user);

        $response = $this->getJson(route(
            Routes::INDEX_SUBTASK,
            [$this->task->uuid]
        ))->assertOk();

        expect($response->json('data'))->toHaveCount(1);
    });

    it('returns empty array if no subtasks', function () {
        $this->actingAs($this->user);

        $response = $this->getJson(route(
            Routes::INDEX_SUBTASK,
            [$this->task->uuid]
        ))->assertOk();

        expect($response->json('data'))->toBeEmpty();
    });

    it('fails if unauthenticated', function () {
        $this->getJson(route(
            Routes::INDEX_SUBTASK,
            [$this->task->uuid]
        ))->assertUnauthorized();
    });
});

// ── Bulk Status ───────────────────────────────────────────────────────────────

describe('PATCH /tasks/bulk-status — bulk update status', function () {

    it('successfully updates multiple task statuses', function () {
        $this->actingAs($this->user);

        $second = Task::factory()->create([
            'project_id' => $this->project->id,
            'reporter_id' => $this->user->id,
            'reference_number' => 2,
            'reference' => 'TEST-2',
            'status' => TaskStatusEnum::BACKLOG->value,
        ]);

        $this->patchJson(route(
            Routes::BULK_UPDATE_TASK_STATUS,
            [$this->project->uuid]
        ), [
            'taskIds' => [$this->task->uuid, $second->uuid],
            'status' => 'in_progress',
        ])->assertOk()
            ->assertJsonPath('data.updated', 2);

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'in_progress']);
        $this->assertDatabaseHas('tasks', ['id' => $second->id, 'status' => 'in_progress']);
    });

    it('only updates tasks belonging to the project', function () {
        $this->actingAs($this->user);

        $otherProject = Project::factory()->create(['organization_id' => $this->organization->id]);
        $otherTask = Task::factory()->create([
            'project_id' => $otherProject->id,
            'reporter_id' => $this->user->id,
            'reference_number' => 1,
            'reference' => 'OTHER-1',
            'status' => TaskStatusEnum::BACKLOG->value,
        ]);

        $response = $this->patchJson(route(
            Routes::BULK_UPDATE_TASK_STATUS,
            [$this->project->uuid]
        ), [
            'taskIds' => [$this->task->uuid, $otherTask->uuid],
            'status' => 'in_progress',
        ])->assertOk();

        expect($response->json('data.updated'))->toBe(1); // only the project's task updated
        $this->assertDatabaseHas('tasks', ['id' => $otherTask->id, 'status' => 'backlog']); // untouched
    });

    it('fails if task_ids is empty', function () {
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::BULK_UPDATE_TASK_STATUS,
            [$this->project->uuid]
        ), ['taskIds' => [], 'status' => 'in_progress'])->assertUnprocessable();
    });

    it('fails if status is invalid', function () {
        $this->actingAs($this->user);

        $this->patchJson(route(
            Routes::BULK_UPDATE_TASK_STATUS,
            [$this->project->uuid]
        ), ['taskIds' => [$this->task->uuid], 'status' => 'invalid'])->assertUnprocessable();
    });

    it('fails if viewer tries to bulk update', function () {
        $this->actingAs($this->viewer);

        $this->patchJson(route(
            Routes::BULK_UPDATE_TASK_STATUS,
            [$this->project->uuid]
        ), ['taskIds' => [$this->task->uuid], 'status' => 'in_progress'])->assertForbidden();
    });
});
