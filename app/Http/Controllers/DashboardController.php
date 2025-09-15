<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function dashboard() {
        $user = auth()->user();
        $deptId = $user->department_id;

        // Card
        $deptEmployeesCount = Employee::whereHas('subDepartment.departments', function ($q) use ($deptId) {
            $q->where('departments.id', $deptId);
        })->count();
        $deptGivenJobsCount = Job::where('job_giver', $user->id)->count();
        $deptReceivedJobsCount = Job::where('department_target_id', $deptId)->count();
        $deptPendingJobsCount = Job::where('department_target_id', $deptId)
            ->whereIn('status', ['pending', 'on_process'])
            ->count();

        // Main content
        // $recentJobs = Job::where('department_target_id', $deptId)
        //     ->orderBy('created_at', 'desc')
        //     ->take(5)
        //     ->get();

        // $deptJobStatusCounts = [
        //     'pending' => Job::where('department_target_id', $deptId)->where('status', 'pending')->count(),
        //     'on_process' => Job::where('department_target_id', $deptId)->where('status', 'on_process')->count(),
        //     'done' => Job::where('department_target_id', $deptId)->where('status', 'done')->count(),
        // ];

        return view('pages.dashboard', compact(
            'deptEmployeesCount',
            'deptGivenJobsCount',
            'deptReceivedJobsCount',
            'deptPendingJobsCount',
            // 'recentJobs',
            // 'deptJobStatusCounts'
        ));
    }
}
