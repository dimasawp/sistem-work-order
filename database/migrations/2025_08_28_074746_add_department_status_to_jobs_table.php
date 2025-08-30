<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('jobs', function (Blueprint $table) {
            $table->foreignId('department_target_id')->after('job_giver')->constrained('departments')->onDelete('cascade');
            $table->enum('status', ['pending', 'on_process', 'done'])->default('pending')->after('end_time');
        });
    }

    public function down(): void {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropForeign(['department_target_id']);
            $table->dropColumn(['department_target_id', 'status']);
        });
    }
};
