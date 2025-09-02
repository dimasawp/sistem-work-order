<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentSubDepartment extends Model {
    protected $table = 'department_sub_department';

    protected $fillable = ['department_id', 'sub_department_id'];

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function subDepartment() {
        return $this->belongsTo(SubDepartment::class);
    }
}
