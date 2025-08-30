<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model {
    use HasFactory;

    protected $fillable = ['name'];

    public function subDepartments() {
        return $this->belongsToMany(SubDepartment::class, 'department_sub_department');
    }

    public function users() {
        return $this->hasMany(User::class);
    }
}
