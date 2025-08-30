<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Schema::create('employees', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('nik')->unique();
        //     $table->string('enroll_id')->nullable();
        //     $table->string('name');
        //     $table->string('kd_bagian')->nullable();
        //     $table->string('kd_jabatan')->nullable();
        //     $table->timestamps();
        // });
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('enroll_id')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('sub_department_id')->nullable();
            // $table->foreignId('sub_department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('position')->nullable(); // ganti kd_jabatan → position
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('employees');
    }
};
