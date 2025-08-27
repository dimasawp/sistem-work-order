<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model {
    protected $fillable = [
        'title',
        'employee_id',
        'job_giver',
        'tools_and_materials',
        'description',
        'start_time',
        'end_time',
    ];

    public function employees() {
        return $this->belongsToMany(Employee::class, 'job_receivers', 'job_id', 'employee_id')
            ->withTimestamps();
    }
}
