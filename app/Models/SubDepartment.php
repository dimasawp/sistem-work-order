<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubDepartment extends Model {
    use HasFactory;

    protected $fillable = ['name'];

    public function departments() {
        return $this->belongsToMany(Department::class, 'department_sub_department');
    }

    public function employees() {
        return $this->hasMany(Employee::class);
    }
}
