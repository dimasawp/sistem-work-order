<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model {
    use HasFactory;

    protected $fillable = [
        'nik',
        'enroll_id',
        'name',
        'sub_department_id',
        'position',
    ];

    public function departments() {
        return $this->hasManyThrough(
            Department::class,
            SubDepartment::class,
            'id',                  // FK di sub_departments
            'id',                  // FK di departments
            'sub_department_id',   // FK di employees
            'id'                   // local key di sub_departments
        );
    }

    public function subDepartment() {
        return $this->belongsTo(SubDepartment::class);
    }

    public function user() {
        return $this->hasOne(User::class, 'nik', 'nik');
    }

    public function jobs() {
        return $this->belongsToMany(Job::class, 'job_receivers', 'employee_id', 'job_id')
            ->withTimestamps();
    }

    public function givenJobs() {
        return $this->hasMany(Job::class, 'employee_id');
    }
}
