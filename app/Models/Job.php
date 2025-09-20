<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'giver_nik',
        'giver_name',
        'tools_and_materials',
        'description',
        'start_time',
        'end_time',
        'department_target_id',
        'status',
    ];

    public function giver() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department() {
        return $this->belongsTo(Department::class, 'department_target_id');
    }

    public function receivers() {
        return $this->belongsToMany(Employee::class, 'job_receivers', 'job_id', 'employee_id');
    }

    protected $appends = ['giver_id', 'giver_display_name'];

    public function getGiverIdAttribute() {
        return $this->giver?->id;
    }

    public function getGiverDisplayNameAttribute() {
        // kalau masih ada relasi
        if ($this->giver && $this->giver->employee) {
            return $this->giver->employee->nik . ' - ' . $this->giver->employee->name;
        }

        // fallback ke snapshot
        if ($this->giver_nik || $this->giver_name) {
            return trim($this->giver_nik . ' - ' . $this->giver_name, ' -');
        }

        return null;
    }
}
