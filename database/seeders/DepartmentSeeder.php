<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder {
    public function run(): void {
        $departments = [
            ['name' => 'Supply Chain'],
            ['name' => 'Business Development'],
            ['name' => 'Produksi'],
            ['name' => 'Finishing'],
            ['name' => 'Research & Development'],
            ['name' => 'Quality Control/Laborat'],
            ['name' => 'Teknik Mekanik'],
            ['name' => 'Teknik Listrik & Instrument, IT'],
            ['name' => 'Pengadaan'],
            ['name' => 'HR-GA-SI'],
            ['name' => 'Finance & Administration'],
            ['name' => 'Pimpinan'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
