<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik')->nullable()->change();
            $table->unsignedBigInteger('department_id')->change();

            $table->foreign('nik')
                ->references('nik')
                ->on('employees')
                ->onDelete('set null');

            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
        });

        // employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_department_id')->nullable()->change();

            $table->foreign('sub_department_id')
                ->references('id')
                ->on('sub_departments')
                ->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['nik']);
            $table->dropForeign(['department_id']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['sub_department_id']);
        });
    }
};
