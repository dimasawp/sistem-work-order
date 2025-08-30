@extends('layouts.app')

@section('title', 'Deliver Job')

@section('content')
<div class="p-4">
    <h3 class="mb-3">Deliver Job</h3>

    <!-- Toggle View -->
    <div class="mb-3 d-flex justify-content-between">
        {{-- <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addJobModal">
            + Tambah Job
        </button> --}}
        <button class="btn btn-success" onclick="openAddJobModal()">+ Tambah Job</button>

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
    <div id="cardContainer" class="row">
        @foreach($jobs as $job)
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm position-relative" style="height: 180px;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex flex-row justify-content-between mb-2">
                            <h5 class="card-title">{{ $job->title }}</h5>
                            
                            <div class="d-flex flex-row gap-2">
                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-warning" onclick="openEditJobModal({{ $job }})">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                
                                <!-- Delete Button -->
                                <form action="{{ route('jobs.destroy', $job->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus job ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="h-100">
                            <p class="card-text">{{ $job->description }}</p>
                        </div>
                        <div>
                            <span class="badge bg-secondary">{{ $job->status }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

     <!-- List View -->
    <div id="listContainer" style="display:none;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobs as $job)
                <tr>
                    <td>{{ $job->title }}</td>
                    <td>{{ $job->description }}</td>
                    <td><span class="badge bg-info">{{ $job->status }}</span></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="openEditJobModal({{ $job }})">
                            <i class="fas fa-pencil-alt"></i>
                        </button>

                       <form action="{{ route('jobs.destroy', $job->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus job ini?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- <div class="modal fade" id="addJobModal" tabindex="-1">
    <div class="modal-dialog"> <!-- bisa tambah in modal -lg biar lebih luas -->
        <form method="POST" action="{{ route('jobs.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden fields -->
                    <input type="hidden" name="job_giver" value="{{ auth()->id() }}">
                    <input type="hidden" name="status" value="pending">

                    <!-- Department Target -->
                    <div class="mb-3">
                        <label>Department Tujuan</label>
                        <select name="department_target_id" class="form-select" required>
                            <option value="">-- Pilih Department --</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Pemberi Job -->
                    <div class="mb-3">
                        <label>Pemberi Job</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->id }}" readonly>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label>Judul</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>

                    <!-- Status Job (Dropdown tapi disabled) -->
                    <div class="mb-3">
                        <label>Status Job</label>
                        <select class="form-select" disabled>
                            <option value="pending" selected>Pending</option>
                        </select>
                    </div>

                    <!-- Pengambil Job (disabled) -->
                    <div class="mb-3">
                        <label>Pengambil Job</label>
                        <input type="text" class="form-control" disabled placeholder="Menunggu penerima">
                    </div>

                    <!-- Alat & Bahan (disabled) -->
                    <div class="mb-3">
                        <label>Alat & Bahan</label>
                        <textarea class="form-control" disabled placeholder="Menunggu penerima"></textarea>
                    </div>

                    <!-- Waktu Mulai & Selesai (disabled) -->
                    <div class="mb-3">
                        <label>Waktu Mulai</label>
                        <input type="datetime-local" class="form-control" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Waktu Selesai</label>
                        <input type="datetime-local" class="form-control" disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div> --}}

{{-- MODAL JOB COMPONENT --}}
<x-job-modal :departments="$departments" />
@endsection

@section('script')
<script>
const cardViewRadio = document.getElementById('cardViewRadio');
const listViewRadio = document.getElementById('listViewRadio');
const cardContainer = document.getElementById('cardContainer');
const listContainer = document.getElementById('listContainer');

cardViewRadio.addEventListener('change', function() {
    if (this.checked) {
        cardContainer.style.display = '';    // kembali ke default (row bootstrap = flex)
        listContainer.style.display = 'none';
    }
});

listViewRadio.addEventListener('change', function() {
    if (this.checked) {
        cardContainer.style.display = 'none';
        listContainer.style.display = '';
    }
});
</script>
@endsection
