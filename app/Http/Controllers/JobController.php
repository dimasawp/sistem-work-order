<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller {
    public function landing() {
        $jobs = Job::with(['receivers:id,nik,name', 'department'])
            ->latest()->get();
        $departments = Department::all();

        return view('pages.landing', compact(
            'jobs',
            'departments',
        ));
    }

    public function search(Request $request) {
        $q = $request->q;
        $jobs = Job::with('department')
            ->where('title', 'like', "%$q%")
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

    public function deliver() {
        $jobs = Job::with(['receivers:id,nik,name'])
            ->where('job_giver', auth()->id())
            ->get();

        $departments = Department::all();
        $userDeptId = auth()->user()->department_id;

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

        $job = new Job();
        $job->title                = $request->title;
        $job->description          = $request->description;
        $job->job_giver            = $request->job_giver;
        $job->department_target_id = $request->department_target_id;
        $job->status               = 'pending'; // override biar aman

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
            'employee_ids'         => 'array',
            'employee_ids.*'       => 'integer|exists:employees,id',
        ]);

        // update core fields (tidak akan override dengan null)
        $job->update([
            'title'                => $request->filled('title') ? $request->title : $job->title,
            'description'          => $request->filled('description') ? $request->description : $job->description,
            'department_target_id' => $request->filled('department_target_id') ? $request->department_target_id : $job->department_target_id,
            'status'               => $request->filled('status') ? $request->status : $job->status,
            'tools_and_materials'  => $request->filled('tools_and_materials') ? $request->tools_and_materials : $job->tools_and_materials,
            'start_time'           => $request->has('start_time')
                ? ($request->start_time ?: null)
                : $job->start_time,
            'end_time'             => $request->has('end_time')
                ? ($request->end_time ?: null)
                : $job->end_time,
        ]);

        // kalau request kirim employee_ids → sync pivot
        if ($request->has('employee_ids')) {
            $job->employees()->sync($request->employee_ids);
        }

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

    /*public function viewCalendar() {
        // Semua karyawan → untuk pemberi job (job_giver)
        $allKaryawan = Employee::select('id', 'nik', 'enroll_id', 'name', 'kd_bagian')->get();

        // Hanya listrik instrumen + outsourcing → untuk pengambil job
        $teknisiListrik = Employee::whereIn('kd_bagian', [
            'TEHNISI LISTRIK INS',
            'TEHNISI OUTSORSING'
        ])->select('id', 'nik', 'enroll_id', 'name', 'kd_bagian')->get();

        return view('index', [
            'allKaryawan' => $allKaryawan,
            'teknisiListrik' => $teknisiListrik,
        ]);
    }


    public function index() {
        $tasks = Job::with('employees')->get()->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->start_time,
                'end' => $task->end_time,
                // 'job_giver' => $task->job_giver ? explode(' - ', $task->job_giver, 2)[1] ?? $task->job_giver : null,
                'job_giver' => $task->job_giver,
                'tools_and_materials' => $task->tools_and_materials,
                'description' => $task->description,
                'employees' => $task->employees->map(function ($emp) {
                    return [
                        'id' => $emp->id,
                        'nik' => $emp->nik,
                        'name' => $emp->name,
                        'kd_bagian' => $emp->kd_bagian,
                    ];
                }),
            ];
        });

        return response()->json($tasks);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'title' => 'required|string',
            'job_giver' => 'nullable|string',
            'tools_and_materials' => 'nullable|string',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'employee_ids' => 'array', // <- array of employee_id
        ]);

        // simpan task
        $task = Job::create([
            'title' => $data['title'],
            'job_giver' => $data['job_giver'] ?? null,
            'tools_and_materials' => $data['tools_and_materials'] ?? null,
            'description' => $data['description'] ?? null,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
        ]);

        // simpan relasi ke pivot
        if (!empty($data['employee_ids'])) {
            $task->employees()->sync($data['employee_ids']);
        }

        return response()->json($task->load('employees'));
    }

    public function update(Request $request, $id) {
        $task = Job::findOrFail($id);

        $task->update($request->only([
            'title',
            'job_giver',
            'tools_and_materials',
            'description',
            'start_time',
            'end_time'
        ]));

        if ($request->has('employee_ids')) {
            $task->employees()->sync($request->employee_ids);
        }

        return response()->json($task->load('employees'));
    }


    public function destroy($id) {
        $task = Job::findOrFail($id);
        $task->employees()->detach();
        $task->delete();

        return response()->json(['message' => 'task deleted']);
    }*/
}
