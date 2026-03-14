<?php

use App\Enums\V1\TaskPriorityEnum;
use App\Enums\V1\TaskStatusEnum;
use App\Enums\V1\TaskTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedInteger('reference_number');
            $table->string('reference');
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->enum('type', array_column(TaskTypeEnum::cases(), 'value'))->default(TaskTypeEnum::TASK->value);
            $table->enum('status', array_column(TaskStatusEnum::cases(), 'value'))->default(TaskStatusEnum::BACKLOG->value);
            $table->enum('priority', array_column(TaskPriorityEnum::cases(), 'value'))->default(TaskPriorityEnum::MEDIUM->value);
            $table->unsignedInteger('story_points')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamp('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['project_id', 'reference']);
            $table->unique(['project_id', 'reference_number']);

            $table->index(['project_id', 'status']);
            $table->index(['assignee_id', 'status']);
            $table->index(['parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'status']);
            $table->dropIndex(['assignee_id', 'status']);
            $table->dropIndex(['parent_id']);
        });
        Schema::dropIfExists('tasks');
    }
};
