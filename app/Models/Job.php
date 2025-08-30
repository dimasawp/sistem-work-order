<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'job_giver',
        'tools_and_materials',
        'description',
        'start_time',
        'end_time',
        'department_target_id',
        'status',
    ];

    /**
     * Employees yang menerima job ini
     */
    public function employees() {
        return $this->belongsToMany(Employee::class, 'job_receivers', 'job_id', 'employee_id')
            ->withTimestamps();
    }

    /**
     * (Opsional) relasi ke Employee sebagai pemberi job
     */
    public function giver() {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Relasi ke department target
     */
    public function department() {
        return $this->belongsTo(Department::class, 'department_target_id');
    }
}
