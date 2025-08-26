<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model {
    protected $fillable = [
        'nik',
        'enroll_id',
        'name',
        'kd_bagian',
        'kd_jabatan'
    ];

    public function events() {
        return $this->belongsToMany(Task::class, 'task_receivers');
    }
}
