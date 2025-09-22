@extends('layouts.app')

@section('title', 'Histori Job')
@section('page-name', 'Histori Job')

@section('content')
    <div class="p-4">
        <h3 class="mb-3">Histori Job</h3>

        <div class="">
            <div class="d-flex justify-content-end align-items-center mb-3">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="fas fa-file-excel me-2"></i> Export Excel
                </button>
            </div>

            <!-- Modal Export -->
            <div class="modal fade" id="exportModal" tabindex="-1">
                <div class="modal-dialog">
                    <form method="GET" action="{{ route('jobs.export') }}">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Export Excel</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Pilih Opsi Export</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_type"
                                            id="exportThisMonth" value="this_month" checked>
                                        <label class="form-check-label" for="exportThisMonth">
                                            Bulan Ini
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_type" id="exportCustom"
                                            value="custom">
                                        <label class="form-check-label" for="exportCustom">
                                            Custom
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tanggal Awal</label>
                                    <input type="date" name="start_date" id="startDate" class="form-control" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Akhir</label>
                                    <input type="date" name="end_date" id="endDate" class="form-control" disabled>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-file-export me-2"></i> Export
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                // Enable/disable date input kalau pilih custom
                document.querySelectorAll('input[name="export_type"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const isCustom = this.value === 'custom';
                        document.getElementById('startDate').disabled = !isCustom;
                        document.getElementById('endDate').disabled = !isCustom;
                    });
                });
            </script>

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
                                <span
                                    class="badge text-white 
                                {{ $job->status == 'pending' ? 'bg-secondary' : ($job->status == 'on_process' ? 'bg-warning' : 'bg-success') }}">
                                    {{ ucfirst($job->status) }}
                                </span>

                                @if ($job->giver_confirmation)
                                    <span
                                        class="badge text-white 
                                        {{ $job->giver_confirmation == 'rejected' ? 'bg-danger' : 'bg-success' }}">
                                        {{ $job->giver_confirmation == 'rejected' ? 'Dikembalikan' : 'Dikonfirmasi' }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">{{ $job->created_at->format('d M Y') }}</td>
                            <td>{{ $job->start_time }}</td>
                            <td>{{ $job->end_time }}</td>
                            <td class="d-flex flex-row justify-content-center gap-2">
                                <button class="btn btn-sm btn-secondary text-white"
                                    onclick='openJobModal(@json($job))'>
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
