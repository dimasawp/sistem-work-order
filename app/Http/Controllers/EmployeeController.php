<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmployeeController extends Controller {
    public function sync() {
        // Contoh fetch ke API payroll
        $response = Http::asForm()->post('http://192.168.11.29/payroll_api/karyawan/index.php', [
            'username_login' => 'pm3', // Bisa pakai env atau dari input
            'password_login' => 'magangpm3',
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Gagal ambil data dari Payroll API');
        }

        $employees = $response->json()['data'];

        foreach ($employees as $emp) {
            Employee::updateOrCreate(
                ['nik' => $emp['NIK']],
                [
                    'enroll_id'  => $emp['EnrollID'] ?? null,
                    'name'       => $emp['Nm_Karyawan'] ?? null,
                    'kd_bagian'  => $emp['Kd_Bagian'] ?? null,
                    'kd_jabatan' => $emp['Kd_Jabatan'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Data karyawan berhasil disinkronisasi');
    }
}
