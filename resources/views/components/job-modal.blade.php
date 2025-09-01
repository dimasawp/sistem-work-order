{{-- @props(['departments', 'mode' => 'giver']) 
<!-- default giver biar gak error -->

<div class="modal fade" id="jobModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="jobForm" method="POST" 
              data-route-store="{{ route('jobs.store') }}" 
              data-route-update="{{ route('jobs.update', ['job' => ':id']) }}">
            @csrf
            <input type="hidden" name="_method" id="jobFormMethod" value="POST">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jobModalTitle">Tambah Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Hidden fields -->
                    @if ($mode === 'giver')
                        <input type="hidden" name="job_giver" id="jobGiver" value="{{ auth()->id() }}">
                        <input type="hidden" name="status" id="jobStatus" value="pending">
                    @endif

                    <!-- Department Target -->
                    <div class="mb-3">
                        <label>Department Tujuan</label>
                        <select name="department_target_id" id="jobDepartment" 
                                class="form-select" 
                                {{ $mode === 'receiver' ? 'disabled' : '' }}
                                required>
                            <option value="">-- Pilih Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pemberi Job -->
                    <div class="mb-3">
                        <label>Pemberi Job</label>
                        <input type="text" id="jobGiverText" class="form-control" 
                            value="{{ $mode === 'giver' ? auth()->user()->id : '' }}" {{ $mode === 'receiver' ? 'disabled' : '' }}>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label>Judul</label>
                        <input type="text" name="title" id="jobTitle" class="form-control" 
                               {{ $mode === 'receiver' ? 'disabled' : '' }} required>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" id="jobDescription" class="form-control" 
                                  {{ $mode === 'receiver' ? 'disabled' : '' }} required></textarea>
                    </div>

                    <!-- Status Job -->
                    <div class="mb-3">
                        <label>Status Job</label>
                        <select name="status" id="jobStatusText" class="form-select"
                                {{ $mode === 'giver' ? 'disabled' : '' }}>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="done">Done</option>
                        </select>
                    </div>

                    <!-- Pengambil Job -->
                    <div class="mb-3">
                        <label>Pengambil Job</label>
                        <input type="text" name="job_taker" id="jobTaker" class="form-control"
                               {{ $mode === 'giver' ? 'disabled' : '' }}>
                    </div>

                    <!-- Alat & Bahan -->
                    <div class="mb-3">
                        <label>Alat & Bahan</label>
                        <textarea name="tools_and_materials" id="jobTools" class="form-control"
                                  {{ $mode === 'giver' ? 'disabled' : '' }}></textarea>
                    </div>

                    <!-- Waktu Mulai & Selesai -->
                    <div class="mb-3">
                        <label>Waktu Mulai</label>
                        <input type="datetime-local" name="start_time" id="jobStartTime" class="form-control"
                               {{ $mode === 'giver' ? 'disabled' : '' }}>
                    </div>
                    <div class="mb-3">
                        <label>Waktu Selesai</label>
                        <input type="datetime-local" name="end_time" id="jobEndTime" class="form-control"
                               {{ $mode === 'giver' ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" id="jobModalSubmit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div> --}}

@props(['departments', 'mode' => 'giver']) 
{{-- default giver biar gak error --}}

<div class="modal fade" id="jobModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="jobForm" method="POST" 
              data-route-store="{{ route('jobs.store') }}" 
              data-route-update="{{ route('jobs.update', ['job' => ':id']) }}">
            @csrf
            <input type="hidden" name="_method" id="jobFormMethod" value="POST">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jobModalTitle">Tambah Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Job Giver (hidden untuk giver, text untuk receiver) -->
                    @if ($mode === 'giver')
                        <input type="hidden" name="job_giver" id="jobGiverHidden" value="{{ auth()->id() }}">
                        <input type="hidden" name="status" id="jobStatus" value="pending">
                    @endif

                    <div class="mb-3">
                        <label>Department Tujuan</label>
                        <select name="department_target_id" id="jobDepartment" 
                                class="form-select" 
                                {{ $mode === 'receiver' ? 'disabled' : '' }}
                                required>
                            <option value="">-- Pilih Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Pemberi Job</label>
                        <input type="text" id="jobGiverText" class="form-control"
                            value="{{ $mode === 'giver' ? auth()->user()->id : '' }}" 
                            {{ $mode === 'receiver' ? 'disabled' : '' }}>
                    </div>

                    <div class="mb-3">
                        <label>Judul</label>
                        <input type="text" name="title" id="jobTitle" class="form-control" 
                               {{ $mode === 'receiver' ? 'disabled' : '' }} required>
                    </div>

                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" id="jobDescription" class="form-control" 
                                  {{ $mode === 'receiver' ? 'disabled' : '' }} required></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Status Job</label>
                        <select name="status" id="jobStatusText" class="form-select"
                                {{ $mode === 'giver' ? 'disabled' : '' }}>
                            <option value="pending">Pending</option>
                            <option value="on_process">On Process</option>
                            <option value="done">Done</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Pengambil Job</label>
                        <input type="text" name="job_taker" id="jobTaker" class="form-control"
                               {{ $mode === 'giver' ? 'disabled' : '' }}>
                    </div>

                    <div class="mb-3">
                        <label>Alat & Bahan</label>
                        <textarea name="tools_and_materials" id="jobTools" class="form-control"
                                  {{ $mode === 'giver' ? 'disabled' : '' }}></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Waktu Mulai</label>
                        <input type="datetime-local" name="start_time" id="jobStartTime" class="form-control"
                               {{ $mode === 'giver' ? 'disabled' : '' }}>
                    </div>
                    <div class="mb-3">
                        <label>Waktu Selesai</label>
                        <input type="datetime-local" name="end_time" id="jobEndTime" class="form-control"
                               {{ $mode === 'giver' ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" id="jobModalSubmit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
