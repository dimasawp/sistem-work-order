<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    protected $fillable = [
        'title',
        'employee_id',
        'task_giver',
        'tools_and_materials',
        'description',
        'start_time',
        'end_time',
    ];

    public function employees() {
        return $this->belongsToMany(Employee::class, 'task_receivers', 'task_id', 'employee_id')
            ->withTimestamps();
    }
}
