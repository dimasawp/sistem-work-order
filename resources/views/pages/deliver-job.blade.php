@extends('layouts.app')

{{-- @section('title', 'Deliver Job') --}}
@section('title', 'Buat Job')
{{-- @section('page-name', 'Deliver Job') --}}
@section('page-name', 'Buat Job')

@section('content')
    <div class="p-4">
        {{-- <h3 class="mb-3">Deliver Job</h3> --}}
        <h3 class="mb-3">Buat Job</h3>

        <!-- Toggle View -->
        <div class="mb-3 d-flex justify-content-between">
            <button class="btn btn-success" onclick="openAddJobModal()"><i class="fa-solid fa-plus me-2"></i> Tambah
                Job</button>

            <div class="btn-group" role="group" aria-label="View Toggle">
                <input type="radio" class="btn-check" name="viewToggle" id="cardViewRadio" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="cardViewRadio">
                    <i class="fas fa-th-large me-1"></i> Card
                </label>

                <input type="radio" class="btn-check" name="viewToggle" id="listViewRadio" autocomplete="off">
                <label class="btn btn-outline-primary" for="listViewRadio">
                    <i class="fas fa-list me-1"></i> List
                </label>
            </div>
        </div>

        <!-- Card View -->
        <div id="cardContainer" class="row g-3">
            @forelse($jobs as $job)
                <div class="col-md-4">
                    <div class="card shadow-sm h-100 position-relative">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <h5 class="card-title mb-0">{{ $job->title }}</h5>
                                    <small class="text-muted">{{ $job->ticket_number }}</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <div>
                                        <button class="btn btn-sm btn-warning text-white"
                                            onclick="openEditJobModal({{ $job }})">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-danger text-white"
                                            onclick="openDeleteModal('{{ route('jobs.destroy', $job->id) }}')" {{ $job->status != 'pending' ? 'disabled' : ''}}>
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-primary align-self-start mb-2">{{ $job->department->name }}</span>
                            <p class="text-secondary small flex-grow-1">{{ Str::limit($job->description, 80) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <p class="small text-muted mb-0">{{ $job->created_at->format('d M Y') }}</p>
                                <span
                                    class="badge {{ $job->status === 'pending' ? 'bg-secondary' : ($job->status === 'on_process' ? 'bg-warning text-dark' : 'bg-success') }}">
                                    {{ ucfirst($job->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">Tidak ada job.</p>
            @endforelse
        </div>

        <!-- List View -->
        <div id="listContainer" style="display:none;">
            <table class="table table-bordered">
                <thead>
                    <tr class="text-center">
                        <th>No. Ticket</th>
                        <th>Departemen</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobs as $job)
                        <tr>
                            <td class="text-center">{{ $job->ticket_number }}</td>
                            <td>{{ $job->department->name }}</td>
                            <td>{{ $job->title }}</td>
                            <td>{{ $job->description }}</td>
                            <td class="text-center">
                                <span
                                    class="badge {{ $job->status == 'pending' ? 'bg-secondary' : ($job->status == 'on_process' ? 'bg-warning' : 'bg-danger') }}">{{ $job->status }}</span>
                            </td>
                            <td class="text-center">{{ $job->created_at->format('d M Y') }}</td>
                            <td class="d-flex flex-row justify-content-center gap-2">
                                <button class="btn btn-sm btn-warning text-white"
                                    onclick='openEditJobModal(@json($job))'>
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger text-white"
                                    onclick="openDeleteModal('{{ route('jobs.destroy', $job->id) }}')" {{ $job->status != 'pending' ? 'disabled' : ''}}>
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL JOB COMPONENT --}}
    <x-job-modal :departments="$departments" mode="giver" />

    {{-- Modal hapus (dihalaman ini saja fitur hapusnya) --}}
    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus job ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const cardViewRadio = document.getElementById('cardViewRadio');
        const listViewRadio = document.getElementById('listViewRadio');
        const cardContainer = document.getElementById('cardContainer');
        const listContainer = document.getElementById('listContainer');

        // fungsi untuk switch view
        function setView(view) {
            if (view === 'card') {
                cardViewRadio.checked = true;
                cardContainer.style.display = '';
                listContainer.style.display = 'none';
            } else {
                listViewRadio.checked = true;
                cardContainer.style.display = 'none';
                listContainer.style.display = '';
            }
            localStorage.setItem('deliverJobView', view); // simpan ke localStorage
        }

        // cek preferensi user saat load halaman
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('deliverJobView') || 'card';
            setView(savedView);
        });

        // event listener untuk toggle
        cardViewRadio.addEventListener('change', function() {
            if (this.checked) setView('card');
        });

        listViewRadio.addEventListener('change', function() {
            if (this.checked) setView('list');
        });

        function openDeleteModal(actionUrl) {
            const form = document.getElementById('deleteForm');
            form.setAttribute('action', actionUrl);
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            modal.show();
        }
    </script>
@endsection
