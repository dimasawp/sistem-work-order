<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Schema::create('jobs', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('title');
        //     $table->string('job_giver');
        //     $table->foreignId('department_target_id')->constrained('departments')->onDelete('cascade');
        //     $table->string('ticket_number')->unique();
        //     $table->text('tools_and_materials')->nullable();
        //     $table->text('description')->nullable();
        //     $table->timestamp('start_time')->nullable();
        //     $table->timestamp('end_time')->nullable();
        //     $table->enum('status', ['pending', 'on_process', 'done'])->default('pending');
        //     $table->enum('giver_confirmation', ['accepted', 'rejected'])->nullable();
        //     // $table->text('giver_note')->nullable();
        //     $table->timestamps();
        // });
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();

            // Snapshot data (disalin saat store guna memudahkan audit trial histori lama)
            $table->string('giver_nik')->nullable();
            $table->string('giver_name')->nullable();

            $table->foreignId('department_target_id')->constrained('departments')->onDelete('cascade');
            $table->string('ticket_number')->unique();
            $table->text('tools_and_materials')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->enum('status', ['pending', 'on_process', 'done'])->default('pending');
            $table->enum('giver_confirmation', ['accepted', 'rejected'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('jobs');
    }
};
