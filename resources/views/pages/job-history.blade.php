@extends('layouts.app')

@section('title', 'Job History')
@section('page-name', 'Job History')

@section('content')
    <div class="p-4">
        <h3 class="mb-3">Job History</h3>
        
        <div class="">
            <div class="d-flex justify-content-end align-items-center mb-3">
                {{-- <h5>Job History</h5> --}}

                <button class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Job</th>
                        <th>Deskripsi</th>
                        <th>Pemberi Job</th>
                        <th>Status</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobHistory as $job)
                        <tr>
                            <td>{{ $job->title }}</td>
                            <td>{{ $job->description }}</td>
                            <td>{{ $job->giver->name ?? '-' }}</td>
                            <td>{{ ucfirst($job->status) }}</td>
                            <td>{{ $job->start_time }}</td>
                            <td>{{ $job->end_time }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center">Belum ada job selesai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
