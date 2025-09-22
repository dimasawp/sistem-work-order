<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
    private function getEmployeesForDepartment($departmentId) {
        $departmentEmployees = Employee::whereHas('subDepartment.departments', function ($q) use ($departmentId) {
            $q->where('departments.id', $departmentId);
        })->with('subDepartment')->get();

        return $departmentEmployees->map(function ($e) {
            return [
                'id' => $e->id,
                'nik' => $e->nik,
                'name' => $e->name,
                'sub_department' => $e->subDepartment->name ?? null,
            ];
        })->values();
    }

    public function profile() {
        $user = auth()->user();

        // ambil semua employee di dept user login
        $employees =  $this->getEmployeesForDepartment($user->department_id);

        return view('pages.profile', [
            'employees' => $employees,
        ]);
    }


    public function updateNik(Request $request) {
        $request->validate([
            'nik' => 'required|exists:employees,nik',
        ]);

        $user = auth()->user();
        $user->nik = $request->nik;
        $user->save();

        // return back()->with('success', 'NIK berhasil diperbarui.');
        return back()->with('toast', [
            'type' => 'success',
            'message' => 'NIK berhasil diperbarui.'
        ]);
    }


    public function updatePassword(Request $request) {
        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user = auth()->user();
        $user->password = $request->password;
        $user->save();

        // return back()->with('success', 'Password berhasil diperbarui.');
        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Password berhasil diperbarui.'
        ]);
    }
}
