@extends('layouts.app')

@section('title', 'Histori Job')
@section('page-name', 'Histori Job')

@section('content')
    <div class="p-4">
        <h3 class="mb-3">Histori Job</h3>
        
        <div class="">
            <div class="d-flex justify-content-end align-items-center mb-3">
                <button class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr class="text-center">
                        <th>No.</th>
                        <th>No. Ticket</th>
                        <th>Departemen</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Pemberi Job</th>
                        <th>Status</th>
                        <th>Tanggal Dibuat</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobHistory as $job)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $job->ticket_number }}</td>
                            <td>{{ $job->department->name ?? '-' }}</td>
                            <td>{{ $job->title }}</td>
                            <td>{{ $job->description }}</td>
                            <td>{{ $job->giver->employee->name ?? $job->user_id }}</td>
                            <td class="text-center">
                                <span class="badge text-white 
                                {{ $job->status == 'pending' ? 'bg-secondary' : 
                                       ($job->status == 'on_process' ? 'bg-warning' : 'bg-success') }}">
                                    {{ ucfirst($job->status) }}
                                </span>
                                
                                @if ($job->giver_confirmation)
                                    <span class="badge text-white 
                                        {{ $job->giver_confirmation == 'rejected' ? 'bg-danger' : 'bg-success' }}">
                                        {{ $job->giver_confirmation == 'rejected' ? 'Dikembalikan' : 'Dikonfirmasi' }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">{{ $job->created_at->format('d M Y') }}</td>
                            <td>{{ $job->start_time }}</td>
                            <td>{{ $job->end_time }}</td>
                            <td class="d-flex flex-row justify-content-center gap-2">
                                <button class="btn btn-sm btn-secondary text-white" onclick='openJobModal(@json($job))'>
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-muted text-center">Belum ada job selesai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-job-modal :departments="$departments ?? []" mode="view" />
@endsection
