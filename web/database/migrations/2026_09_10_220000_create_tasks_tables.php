<?php

use App\Enums\TaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('name', 160);
            $table->text('description');
            $table->dateTime('due_at')->nullable()->index();
            $table->boolean('show_in_calendar')->default(false);
            $table->string('priority', 20)->nullable()->index();
            $table->string('status', 30)->default(TaskStatus::NotStarted->value)->index();
            $table->string('recurrence', 20)->nullable();
            $table->date('recurrence_ends_at')->nullable();
            $table->foreignId('previous_occurrence_id')->nullable()->unique()->constrained('tasks')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['project_id', 'deleted_at', 'status']);
        });

        Schema::create('task_user', function (Blueprint $table) {
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['task_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_user');
        Schema::dropIfExists('tasks');
    }
};
