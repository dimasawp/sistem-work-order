<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller {
    public function deliver() {
        $jobs = Job::where('job_giver', auth()->id())->get();
        $departments = Department::all(); // untuk dropdown target dept

        return view('pages.deliver-job', compact('jobs', 'departments'));
    }

    // Simpan Job Baru
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
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'department_target_id' => 'required|integer|exists:departments,id',
            'status'               => 'in:pending,on_process,done',
            'tools_and_materials'  => 'nullable|string',
            'start_time'           => 'nullable|date',
            'end_time'             => 'nullable|date|after_or_equal:start_time',
            'redirect_to'          => 'nullable|string'
        ]);

        // Update fields
        $job->title                = $request->title;
        $job->description          = $request->description;
        $job->department_target_id = $request->department_target_id;
        $job->status               = $request->status ?? $job->status;
        $job->tools_and_materials  = $request->tools_and_materials;
        $job->start_time           = $request->start_time;
        $job->end_time             = $request->end_time;

        $job->save();

        // Tentukan redirect, default ke deliver
        $redirectRoute = $request->redirect_to ?? 'jobs.deliver';

        return redirect()->route($redirectRoute)->with('success', 'Job berhasil diperbarui!');
    }

    public function destroy(Job $job) {
        $job->delete();
        return redirect()->back()->with('success', 'Job berhasil dihapus!');
    }

    public function received() {
        $departments = Department::all();
        $userDeptId = auth()->user()->department_id;

        // list kiri: pending & on_process
        $receivedJobs = Job::where('department_target_id', $userDeptId)
            ->where('status', '!=', 'done')
            ->get();

        // events di calendar
        $assignedJobs = Job::where('department_target_id', $userDeptId)
            ->whereNotNull('start_time')
            ->whereIn('status', ['pending', 'on_process'])
            ->get()
            ->map(function ($job) {
                return [
                    'id'    => $job->id,
                    'title' => $job->title,
                    'start' => $job->start_time,
                    'end'   => $job->end_time,
                    'job'   => [
                        'id' => $job->id,
                        'title' => $job->title,
                        'description' => $job->description,
                        'job_giver' => $job->job_giver,
                        'job_receiver' => $job->job_receiver,
                        'status' => $job->status,   // pastikan ini ikut
                        'start_time' => $job->start_time,
                        'end_time' => $job->end_time,
                    ],
                ];
            });


        // job history: status done
        $jobHistory = Job::where('department_target_id', $userDeptId)
            ->where('status', 'done')
            ->get();

        return view('pages.job-received', compact('receivedJobs', 'assignedJobs', 'jobHistory', 'departments'));
    }

    public function updateTime(Request $request, Job $job) {
        // dd($job);
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
