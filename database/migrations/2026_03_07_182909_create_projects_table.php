<?php

use App\Enums\V1\ProjectStatusEnum;
use App\Enums\V1\ProjectVisibilityEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('key');
            $table->text('description')->nullable();
            $table->enum('status', array_column(ProjectStatusEnum::cases(), 'value'))
                ->default(ProjectStatusEnum::ACTIVE->value);
            $table->enum('visibility', array_column(ProjectVisibilityEnum::cases(), 'value'))
                ->default(ProjectVisibilityEnum::PRIVATE->value);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
