<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\SubDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmployeeController extends Controller {
    public function index() {
        $userDeptId = auth()->user()->department_id;

        $employees = Employee::whereHas('subDepartment.departments', function ($q) use ($userDeptId) {
            $q->where('departments.id', $userDeptId);
        })
            ->with('subDepartment') // biar gak N+1 query
            ->paginate(10); // <--- paginate 20 per page

        return view('pages.employee', compact('employees'));
    }

    public function sync() {
        // Contoh fetch ke API payroll
        $response = Http::asForm()->post('http://192.168.11.29/payroll_api/karyawan/index.php', [
            'username_login' => 'pm3',
            'password_login' => 'magangpm3',
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Gagal ambil data dari Payroll API');
        }

        $employees = $response->json()['data'];

        // Mapping kd_bagian → department
        $mapping = [
            'sc' => ['PPC', 'PPC OUTSORSING', 'CHEMICAL', 'CHEMICAL OUTSORSING', 'GUD.JADI SEC OUTSOR', 'GUDANG BB OS', 'GUDANG SP', 'GUDANG SP OUTSORSING'],
            'bd' => ['CUSTAMER SERVICE'],
            'fa' => ['CC OS', 'COST CONTROL'],
            'fs' => ['FINISHING', 'FINISHING OS B', 'FINISHING OUTSORSING'],
            'hr' => ['HR/UMUM', 'PDD', 'UMUM FORKLIF OS', 'UMUM OUTSORSING', 'UMUM SOPIR OS'],
            'rd' => ['LAB R&D', 'LAB R&D OS'],
            'qc' => ['LABORAT', 'LABORAT OUTSORSING'],
            'pd' => ['PENGADAAN', 'PENGADAAN OUTSORSING'],
            'pm' => ['PIMPINAN'],
            'pp' => ['PM 3', 'PM 3 ADDITIVE', 'PM 3 HYDRA', 'PM 3 MS POT', 'PM 3 OUTSORSING', 'PM 3 PELAKSANA', 'PM 3 PENGAWAS', 'PM 3 REFINER', 'PM 3 UPL', 'PM 3 WKL'],
            'tm' => ['TEHNISI', 'TEHNISI BOILER A GAB', 'TEHNISI OS BOILER', 'TEHNISI OUTSORSING'],
            'tl' => ['TEHNISI LISTRIK INS', 'TEHNISI OUTSORSING'],
        ];

        // Cocokkan kd dengan department_id (sesuaikan dengan tabel departments yang kamu punya)
        $deptMap = [
            'sc' => 'Supply Chain',
            'bd' => 'Business Development',
            'fa' => 'Finance & Administration',
            'fs' => 'Finishing',
            'hr' => 'HR-GA-SI',
            'rd' => 'Research & Development',
            'qc' => 'Quality Control/Laborat',
            'pd' => 'Pengadaan',
            'pm' => 'Pimpinan',
            'pp' => 'Produksi',
            'tm' => 'Teknik Mekanik',
            'tl' => 'Teknik Listrik & Instrument, IT',
        ];

        foreach ($employees as $emp) {
            $kdBagian = strtoupper(trim($emp['Kd_Bagian'] ?? ''));
            if (!$kdBagian) continue;

            // 1. Cari / buat SubDepartment sesuai nama asli kd_bagian
            $subDept = SubDepartment::firstOrCreate(['name' => $kdBagian]);

            // 2. Cari groupKey berdasarkan substring (contain)
            $groupKey = null;
            foreach ($mapping as $key => $keywords) {
                foreach ($keywords as $kw) {
                    if (stripos($kdBagian, $kw) !== false) {
                        $groupKey = $key;
                        break 2; // keluar dari 2 loop sekaligus
                    }
                }
            }

            // 3. Jika ketemu groupKey → hubungkan subDept dengan Department
            if ($groupKey && isset($deptMap[$groupKey])) {
                $deptName = $deptMap[$groupKey];
                $department = Department::firstOrCreate(['name' => $deptName]);

                $subDept->departments()->syncWithoutDetaching([$department->id]);
            }

            // 4. Simpan / update employee
            Employee::updateOrCreate(
                ['nik' => $emp['NIK']],
                [
                    'enroll_id'         => $emp['EnrollID'] ?? null,
                    'name'              => $emp['Nm_Karyawan'] ?? null,
                    'sub_department_id' => $subDept->id,
                    'position'          => $emp['Kd_Jabatan'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Data karyawan berhasil disinkronisasi');
    }
}
