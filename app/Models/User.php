<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasRoles, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'nik',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function employee() {
        return $this->belongsTo(Employee::class, 'nik', 'nik');
    }
}
