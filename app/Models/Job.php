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

    // protected $casts = [
    //     'start_time' => 'datetime',
    //     'end_time'   => 'datetime',
    // ];

    public function employees() {
        return $this->belongsToMany(Employee::class, 'job_receivers', 'job_id', 'employee_id')
            ->withTimestamps();
    }

    public function giver() {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function department() {
        return $this->belongsTo(Department::class, 'department_target_id');
    }

    public function receivers() {
        return $this->belongsToMany(Employee::class, 'job_receivers', 'job_id', 'employee_id');
    }
}
