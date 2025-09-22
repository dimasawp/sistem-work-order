<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Job;
use App\Models\User;
use App\Exports\JobsExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JobController extends Controller {
    public function landing() {
        $jobs = Job::with(['receivers:id,nik,name', 'department', 'giver.employee'])
            ->latest()->take(10)->get();
        // dd($jobs);
        $departments = Department::all();

        return view('pages.landing', compact(
            'jobs',
            'departments',
        ));
    }

    public function search(Request $request) {
        $q = $request->q;
        $jobs = Job::with('department')
            ->where('ticket_number', 'like', "%$q%")
            ->latest()->get();
        $departments = Department::all();

        return view('pages.landing', compact('jobs', 'departments'));
    }

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

    private function generateTicketNumber($departmentId) {
        // mapping kode departemen
        $deptMap = [
            'sc' => 'SUPPLY CHAIN',
            'bd' => 'BUSINESS DEVELOPMENT',
            'fa' => 'FINANCE & ADMINISTRATION',
            'fs' => 'FINISHING',
            'hr' => 'HR-GA-SI',
            'rd' => 'RESEARCH & DEVELOPMENT',
            'qc' => 'QUALITY CONTROL/LABORAT',
            'pd' => 'PENGADAAN',
            'pm' => 'PIMPINAN',
            'pp' => 'PRODUKSI',
            'tm' => 'TEKNIK MEKANIK',
            'tl' => 'TEKNIK LISTRIK & INSTRUMENT, IT',
        ];

        $department = Department::findOrFail($departmentId);

        // cari key dari nama department
        $deptCode = collect($deptMap)
            ->search(strtoupper($department->name));

        if (!$deptCode) {
            // fallback kalau nama tidak cocok
            $deptCode = strtoupper(substr($department->name, 0, 2));
        } else {
            $deptCode = strtoupper($deptCode);
        }

        $today = Carbon::now();
        $year = $today->format('Y');
        $month = $today->format('m');
        $day = $today->format('d');

        // hitung jumlah job bulan ini untuk dept tersebut
        $countThisMonth = Job::where('department_target_id', $departmentId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $sequence = str_pad($countThisMonth + 1, 3, '0', STR_PAD_LEFT);

        return "{$sequence}/{$deptCode}/{$year}/{$month}/{$day}";
    }

    public function generateTicket($deptId) {
        $ticket = $this->generateTicketNumber($deptId);
        return response()->json(['ticket_number' => $ticket]);
    }

    public function deliver() {
        $jobs = Job::with(['receivers:id,nik,name'])
            ->where('user_id', auth()->id())
            ->get();

        $userDeptId = auth()->user()->department_id;
        // $departments = Department::all();
        $departments = Department::where('id', '!=', $userDeptId)
            ->whereHas('users.roles', function ($query) {
                $query->where('name', 'job-receiver');
            })
            ->get();

        $employeesForJs = $this->getEmployeesForDepartment($userDeptId);

        return view('pages.deliver-job', compact('jobs', 'departments', 'employeesForJs'));
    }

    public function received() {
        $jobs = Job::with(['receivers:id,nik,name'])->get();

        $departments = Department::all();
        $userDeptId = auth()->user()->department_id;

        $receivedJobs = Job::with(['receivers:id,nik,name'])
            ->where('department_target_id', $userDeptId)
            ->where(function ($q) {
                $q->where('status', '!=', 'done')
                    ->orWhereNull('giver_confirmation') // include yang belum dikonfirmasi
                    ->orWhere('giver_confirmation', '!=', 'accepted');
            })
            ->get();

        $assignedJobs = Job::with(['receivers:id,nik,name'])
            ->where('department_target_id', $userDeptId)
            ->whereNotNull('start_time')
            // ->whereIn('status', ['pending', 'on_process'])
            ->get()
            ->map(fn($job) => [
                'id' => $job->id,
                'title' => $job->title,
                'start' => $job->start_time,
                'end' => $job->end_time,
                'job' => $job,
            ]);

        $employeesForJs = $this->getEmployeesForDepartment($userDeptId);

        return view('pages.job-received', compact(
            'jobs',
            'receivedJobs',
            'assignedJobs',
            'departments',
            'employeesForJs'
        ));
    }

    public function history() {
        $userDeptId = auth()->user()->department_id;

        $jobHistory = Job::with(['receivers:id,nik,name', 'department'])
            ->where('department_target_id', $userDeptId)
            ->where('status', 'done')
            ->where('giver_confirmation', 'accepted')
            ->get();

        $departments = Department::all();
        $employeesForJs = $this->getEmployeesForDepartment($userDeptId);

        return view('pages.job-history', compact('jobHistory', 'departments', 'employeesForJs'));
    }

    public function store(Request $request) {
        $request->validate([
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'user_id'              => 'required|integer|exists:users,id',
            'department_target_id' => 'required|integer|exists:departments,id',
            'status'               => 'in:pending,on_process,done'
        ]);

        $ticketNumber = $this->generateTicketNumber($request->department_target_id);

        // ambil user
        $user = User::with('employee')->findOrFail($request->user_id);

        $job = new Job();
        $job->title                = $request->title;
        $job->description          = $request->description;
        $job->user_id              = $request->user_id;
        $job->department_target_id = $request->department_target_id;
        $job->status               = 'pending'; // override biar aman
        $job->ticket_number        = $ticketNumber;

        // snapshot pemberi job
        $job->giver_nik   = $user->employee->nik ?? null;
        $job->giver_name  = $user->employee->name ?? $user->name ?? null;

        // bagian penerima → null dulu
        $job->tools_and_materials  = null;
        $job->start_time           = null;
        $job->end_time             = null;

        $job->save();

        return redirect()->route('jobs.deliver')->with('toast', [
            'type' => 'success',
            'message' => 'Job berhasil ditambahkan.'
        ]);
    }

    public function update(Request $request, Job $job) {
        $request->validate([
            'title'                => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'department_target_id' => 'nullable|integer|exists:departments,id',
            'status'               => 'in:pending,on_process,done',
            'tools_and_materials'  => 'nullable|string',
            'start_time'           => 'nullable|date',
            'end_time'             => 'nullable|date|after_or_equal:start_time',
            'employee_ids'         => 'nullable|array',
            'employee_ids.*'       => 'nullable|integer|exists:employees,id',
            'user_id'              => 'nullable|integer|exists:users,id', // optional kalau mau ganti pemberi
        ]);

        // Kalau user_id ikut diupdate → perbarui snapshot
        if ($request->has('user_id')) {
            $user = \App\Models\User::with('employee')->findOrFail($request->user_id);
            $job->user_id     = $request->user_id;
            $job->giver_nik   = $user->employee->nik ?? null;
            $job->giver_name  = $user->employee->name ?? $user->name ?? null;
        }

        // Update field lain
        $job->update([
            'title'                => $request->has('title') ? $request->title : $job->title,
            'description'          => $request->has('description') ? $request->description : $job->description,
            'department_target_id' => $request->has('department_target_id') ? $request->department_target_id : $job->department_target_id,
            'status'               => $request->has('status') ? $request->status : $job->status,
            'tools_and_materials'  => $request->has('tools_and_materials') ? $request->tools_and_materials : $job->tools_and_materials,
            'start_time'           => $request->has('start_time') ? ($request->start_time ?: null) : $job->start_time,
            'end_time'             => $request->has('end_time') ? ($request->end_time ?: null) : $job->end_time,
        ]);

        // Sync penerima (relasi many-to-many)
        $job->receivers()->sync($request->input('employee_ids', []));

        $redirectRoute = $request->redirect_to ?? 'jobs.deliver';
        return redirect()->route($redirectRoute)->with('toast', [
            'type' => 'success',
            'message' => 'Job berhasil diperbarui.'
        ]);
    }

    public function confirm(Job $job) {
        $job->giver_confirmation = 'accepted';
        $job->save();

        return redirect()->back()->with('toast', [
            'type' => 'success',
            'message' => 'Job berhasil dikonfirmasi.'
        ]);
    }

    public function reject(Job $job) {
        $job->giver_confirmation = 'rejected';
        $job->status = 'on_process'; // atau status lain sesuai kebutuhan
        $job->save();

        return redirect()->back()->with('toast', [
            'type' => 'warning',
            'message' => 'Job ditolak.'
        ]);
    }

    public function destroy(Job $job) {
        $job->delete();
        return redirect()->back()->with('toast', [
            'type' => 'success',
            'message' => 'Job berhasil dihapus.'
        ]);
    }

    public function updateTime(Request $request, Job $job) {
        try {
            $request->validate([
                'start_time' => 'required|date',
                'end_time'   => 'nullable|date|after_or_equal:start_time',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }
        $request->validate([
            'start_time' => 'required|date',
            'end_time'   => 'nullable|date|after_or_equal:start_time',
        ]);

        $job->start_time = $request->start_time;
        $job->end_time   = $request->end_time;

        // kalau status pending, ubah ke on_process
        if ($job->status === 'pending') {
            $job->status = 'on_process';
        }
        $job->save();

        return response()->json(['success' => true, 'job' => $job]);
    }

    public function export(Request $request) {
        $userDeptId = auth()->user()->department_id;

        $query = Job::where('department_target_id', $userDeptId);

        if ($request->export_type === 'this_month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
            $filename = "Jobs_" . now()->format('F') . ".xlsx";
        }

        if ($request->export_type === 'custom') {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
            $filename = "Jobs_{$request->start_date}_{$request->end_date}.xlsx";
        }

        $jobs = $query->get();

        return Excel::download(new JobsExport($jobs), $filename);
    }
}
