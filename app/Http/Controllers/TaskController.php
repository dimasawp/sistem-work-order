<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller {
    public function viewCalendar() {
        // Semua karyawan → untuk pemberi job (task_giver)
        $allKaryawan = Employee::select('id', 'nik', 'enroll_id', 'name', 'kd_bagian')->get();

        // Hanya listrik instrumen + outsourcing → untuk pengambil job
        $teknisiListrik = Employee::whereIn('kd_bagian', [
            'TEHNISI LISTRIK INS',
            'TEHNISI OUTSORSING'
        ])->select('id', 'nik', 'enroll_id', 'name', 'kd_bagian')->get();

        return view('calendar', [
            'allKaryawan' => $allKaryawan,
            'teknisiListrik' => $teknisiListrik,
        ]);
    }


    public function index() {
        $tasks = Task::with('employees')->get()->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->start_time,
                'end' => $task->end_time,
                // 'task_giver' => $task->task_giver ? explode(' - ', $task->task_giver, 2)[1] ?? $task->task_giver : null,
                'task_giver' => $task->task_giver,
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
            'task_giver' => 'nullable|string',
            'tools_and_materials' => 'nullable|string',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'employee_ids' => 'array', // <- array of employee_id
        ]);

        // simpan task
        $task = Task::create([
            'title' => $data['title'],
            'task_giver' => $data['task_giver'] ?? null,
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
        $task = Task::findOrFail($id);

        $task->update($request->only([
            'title',
            'task_giver',
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
        $task = Task::findOrFail($id);
        $task->employees()->detach();
        $task->delete();

        return response()->json(['message' => 'task deleted']);
    }
}
