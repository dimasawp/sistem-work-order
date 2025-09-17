<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JobController extends Controller {
    public function landing() {
        $jobs = Job::with(['receivers:id,nik,name', 'department'])
            ->latest()->take(10)->get();
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

        return view('pages.landing', compact('jobs'));
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
            ->where('job_giver', auth()->id())
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
            ->where('status', '!=', 'done')
            ->get();

        $assignedJobs = Job::with(['receivers:id,nik,name'])
            ->where('department_target_id', $userDeptId)
            ->whereNotNull('start_time')
            ->whereIn('status', ['pending', 'on_process'])
            ->get()
            ->map(fn($job) => [
                'id' => $job->id,
                'title' => $job->title,
                'start' => $job->start_time,
                'end' => $job->end_time,
                'job' => $job,
            ]);


        $jobHistory = Job::where('department_target_id', $userDeptId)
            ->where('status', 'done')
            ->get();

        $employeesForJs = $this->getEmployeesForDepartment($userDeptId);

        return view('pages.job-received', compact(
            'jobs',
            'receivedJobs',
            'assignedJobs',
            'jobHistory',
            'departments',
            'employeesForJs'
        ));
    }

    public function history() {
        $userDeptId = auth()->user()->department_id;

        $jobHistory = Job::where('department_target_id', $userDeptId)
            ->where('status', 'done')
            ->get();

        $departments = Department::all();
        $employeesForJs = $this->getEmployeesForDepartment($userDeptId);

        return view('pages.job-history', compact('jobHistory', 'departments', 'employeesForJs'));
    }

    public function store(Request $request) {
        $request->validate([
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'job_giver'            => 'required|integer|exists:users,id',
            'department_target_id' => 'required|integer|exists:departments,id',
            'status'               => 'in:pending,on_process,done'
        ]);

        $ticketNumber = $this->generateTicketNumber($request->department_target_id);

        $job = new Job();
        $job->title                = $request->title;
        $job->description          = $request->description;
        $job->job_giver            = $request->job_giver;
        $job->department_target_id = $request->department_target_id;
        $job->status               = 'pending'; // override biar aman
        $job->ticket_number        = $ticketNumber;

        // bagian penerima → null dulu
        $job->tools_and_materials  = null;
        $job->start_time           = null;
        $job->end_time             = null;

        $job->save();

        return redirect()->route('jobs.deliver')->with('success', 'Job berhasil ditambahkan!');
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
        ]);

        $job->update([
            'title'                => $request->has('title') ? $request->title : $job->title,
            'description'          => $request->has('description') ? $request->description : $job->description,
            'department_target_id' => $request->has('department_target_id') ? $request->department_target_id : $job->department_target_id,
            'status'               => $request->has('status') ? $request->status : $job->status,
            'tools_and_materials'  => $request->has('tools_and_materials') ? $request->tools_and_materials : $job->tools_and_materials,
            'start_time'           => $request->has('start_time') ? ($request->start_time ?: null) : $job->start_time,
            'end_time'             => $request->has('end_time') ? ($request->end_time ?: null) : $job->end_time,
        ]);

        // Selalu sync, meskipun kosong
        $job->employees()->sync($request->input('employee_ids', []));

        $redirectRoute = $request->redirect_to ?? 'jobs.deliver';
        return redirect()->route($redirectRoute)->with('success', 'Job berhasil diperbarui!');
    }

    public function destroy(Job $job) {
        $job->delete();
        return redirect()->back()->with('success', 'Job berhasil dihapus!');
    }

    public function updateTime(Request $request, Job $job) {
        // dd($job);
        try {
            //code...
            $request->validate([
                'start_time' => 'required|date',
                'end_time'   => 'nullable|date|after_or_equal:start_time',
            ]);
        }
        //throw $th;
        catch (\Illuminate\Validation\ValidationException $e) {
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
}
