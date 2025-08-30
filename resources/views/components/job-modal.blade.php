@props(['departments'])
<div class="modal fade" id="jobModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="jobForm" method="POST" data-route-store="{{ route('jobs.store') }}" data-route-update="{{ route('jobs.update', ['job' => ':id']) }}">
            @csrf
            <input type="hidden" name="_method" id="jobFormMethod" value="POST">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jobModalTitle">Tambah Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Hidden fields -->
                    <input type="hidden" name="job_giver" id="jobGiver" value="{{ auth()->id() }}">
                    <input type="hidden" name="status" id="jobStatus" value="pending">

                    <!-- Department Target -->
                    <div class="mb-3">
                        <label>Department Tujuan</label>
                        <select name="department_target_id" id="jobDepartment" class="form-select" required>
                            <option value="">-- Pilih Department --</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Pemberi Job -->
                    <div class="mb-3">
                        <label>Pemberi Job</label>
                        <input type="text" id="jobGiverText" class="form-control" value="{{ auth()->user()->id }}" readonly>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label>Judul</label>
                        <input type="text" name="title" id="jobTitle" class="form-control" required>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" id="jobDescription" class="form-control" required></textarea>
                    </div>

                    <!-- Status Job (Dropdown tapi disabled) -->
                    <div class="mb-3">
                        <label>Status Job</label>
                        <select id="jobStatusText" class="form-select" disabled>
                            <option value="pending" selected>Pending</option>
                        </select>
                    </div>

                    <!-- Pengambil Job (disabled) -->
                    <div class="mb-3">
                        <label>Pengambil Job</label>
                        <input type="text" id="jobTaker" class="form-control" disabled placeholder="Menunggu penerima">
                    </div>

                    <!-- Alat & Bahan (disabled) -->
                    <div class="mb-3">
                        <label>Alat & Bahan</label>
                        <textarea id="jobTools" class="form-control" disabled placeholder="Menunggu penerima"></textarea>
                    </div>

                    <!-- Waktu Mulai & Selesai (disabled) -->
                    <div class="mb-3">
                        <label>Waktu Mulai</label>
                        <input type="datetime-local" id="jobStartTime" class="form-control" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Waktu Selesai</label>
                        <input type="datetime-local" id="jobEndTime" class="form-control" disabled>
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
