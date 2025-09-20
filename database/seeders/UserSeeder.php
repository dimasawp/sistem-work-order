<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;

class UserSeeder extends Seeder {
    public function run(): void {
        $departments = [
            'Supply Chain' => 'sc',
            'Business Development' => 'bd',
            'Produksi' => 'pp',
            'Finishing' => 'fs',
            'Research & Development' => 'rd',
            'Quality Control/Laborat' => 'qc',
            'Teknik Mekanik' => 'tm',
            'Teknik Listrik & Instrument, IT' => 'tl',
            'Pengadaan' => 'pd',
            'HR-GA-SI' => 'hr',
            'Finance & Administration' => 'fa',
            'Pimpinan' => 'pm',
        ];

        foreach ($departments as $name => $initial) {
            $dept = Department::where('name', $name)->first();

            if ($dept) {
                $user = User::create([
                    'username'      => 'admin-' . $initial,
                    // 'password'      => Hash::make('password'),
                    'password'      => 'password',
                    'department_id' => $dept->id,
                    'nik'           => null,
                ]);

                // Role assignment
                if (in_array($initial, ['pp', 'fs'])) {
                    // hanya pemberi job
                    $user->assignRole('job-giver');
                } else {
                    // bisa keduanya
                    $user->assignRole(['job-giver', 'job-receiver']);
                }
            }
        }
    }
}
