@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-name', 'Dashboard')

@section('content')
    <div class="p-4">
        <h3 class="mb-4">Dashboard</h3>

        <div class="row g-3 mb-4">
            <!-- Card 1: Jumlah Karyawan Dept -->
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted">Karyawan Dept</p>
                            <h1 class="mt-1">{{ $deptEmployeesCount }}</h1>
                        </div>
                        <div class="bg-dark text-white rounded p-2 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Job Dibuat -->
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted">Job Dibuat</p>
                            <h1 class="mt-1">{{ $deptGivenJobsCount }}</h1>
                        </div>
                        <div class="bg-dark text-white rounded p-2 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-clipboard-list fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Job Diterima -->
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted">Job Diterima</p>
                            <h1 class="mt-1">{{ $deptReceivedJobsCount }}</h1>
                        </div>
                        <div class="bg-dark text-white rounded p-2 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-inbox fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Job Selesai -->
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted">Job On Proses</p>
                            <h1 class="mt-1">{{ $deptPendingJobsCount }}</h1>
                        </div>
                        <div class="bg-dark text-white rounded p-2 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Main Content -->
        {{-- <div class="row g-4">
            <!-- Grafik Summary Job -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="mb-3">Status Job</h5>
                        <canvas id="jobStatusChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- List Job Terbaru / Mendekati Deadline -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="mb-3">Job Terbaru</h5>
                        <ul class="list-group list-group-flush">
                            @foreach ($recentJobs as $job)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $job->title }}</strong><br>
                                        <small class="text-muted">{{ $job->department->name ?? '-' }}</small>
                                    </div>
                                    <span
                                        class="badge {{ $job->status == 'pending' ? 'bg-secondary' : ($job->status == 'on_process' ? 'bg-warning' : 'bg-success') }}">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                </li>
                            @endforeach
                            @if (count($recentJobs) == 0)
                                <li class="list-group-item text-center text-muted">Tidak ada job terbaru</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('jobStatusChart').getContext('2d');
        const jobStatusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'On Process', 'Done'],
                datasets: [{
                    data: [
                        {{ $deptJobStatusCounts['pending'] ?? 0 }},
                        {{ $deptJobStatusCounts['on_process'] ?? 0 }},
                        {{ $deptJobStatusCounts['done'] ?? 0 }}
                    ],
                    backgroundColor: ['#6c757d', '#ffc107', '#198754'],
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
@endpush
