@props(['departments', 'mode' => 'giver'])
{{-- mode bisa: giver | receiver | view --}}
{{-- default giver --}}

<div class="modal fade" id="jobModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="jobForm" method="POST" data-route-store="{{ route('jobs.store') }}"
            data-route-update="{{ route('jobs.update', ['job' => ':id']) }}">
            @csrf
            <input type="hidden" name="_method" id="jobFormMethod" value="POST">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jobModalTitle">Tambah Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @if ($mode === 'giver')
                        <input type="hidden" name="user_id" id="jobGiverHidden" value="{{ auth()->id() }}">
                        <input type="hidden" name="status" id="jobStatus" value="pending">
                        <input type="hidden" name="redirect_to" value="jobs.deliver">
                    @else
                        <input type="hidden" name="redirect_to" value="jobs.received">
                    @endif

                    <div class="row">
                        <div class="col-6" id="leftColumn">
                            <div class="mb-3">
                                <label>Department Tujuan</label>
                                <select name="department_target_id" id="jobDepartment" class="form-select" required>
                                    <option value="">-- Pilih Department --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jobTicketNumber" class="form-label">Nomor Ticket</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="jobTicketNumber" name="ticket_number"
                                        readonly>
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="copyTicket()">Copy</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Pemberi Job</label>
                                <!-- Hidden untuk ID -->
                                <input type="hidden" id="jobGiverHidden" name="giver_id">

                                <!-- Text buat tampil nama -->
                                <input type="text" id="jobGiverText" class="form-control" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Judul</label>
                                <input type="text" name="title" id="jobTitle" class="form-control" required>
                            </div>
                            <div class="mb-3 d-flex flex-column">
                                <label>Deskripsi</label>
                                <textarea name="description" id="jobDescription" class="form-control" rows="6" required></textarea>
                            </div>
                        </div>
                        <div class="col-6" id="rightColumn">
                            <div class="mb-3">
                                <label>Status Job</label>
                                <select name="status" id="jobStatusText" class="form-select">
                                    <option value="pending">Pending</option>
                                    <option value="on_process">On Process</option>
                                    <option value="done">Done</option>
                                </select>
                            </div>
                            <div class="mb-3 position-relative">
                                <label for="employee_search" class="form-label">Pengambil Job</label>
                                <input type="text" class="form-control" id="employee_search" autocomplete="off"
                                    placeholder="Ketik nama / NIK...">
                                <div id="employeeSuggestions" class="list-group position-absolute w-100"
                                    style="z-index: 1000;">
                                </div>
                                <!-- Tempat menaruh chips/banner -->
                                <div id="selectedEmployees" class="mt-2 d-flex flex-wrap gap-2"></div>
                            </div>
                            <div class="mb-3">
                                <label>Alat & Bahan</label>
                                <textarea name="tools_and_materials" id="jobTools" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Waktu Mulai</label>
                                <input type="datetime-local" name="start_time" id="jobStartTime" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Waktu Selesai</label>
                                <input type="datetime-local" name="end_time" id="jobEndTime" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    @if ($mode == 'giver')
                        <div id="confirmJobForm" class="d-flex gap-2 d-none">
                            <button type="button" class="btn btn-success" id="btnConfirmJob">Konfirmasi</button>
                            <button type="button" class="btn btn-danger" id="btnRejectJob">Tolak</button>
                        </div>
                    @endif
                    <button type="submit" id="jobModalSubmit" class="btn btn-success">Simpan</button>
                    <button type="button" id="jobModalCancel" class="btn btn-secondary"
                        data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>

    window.jobMode = "{{ $mode }}";
    // Setelah pilih employee
    function setEmployeeIds(employeeIds) {
        const container = document.getElementById("selectedEmployees");
        const form = document.getElementById("jobForm");
        // Hapus hidden input lama
        form.querySelectorAll('input[name="employee_ids[]"]').forEach(el => el.remove());
        // Tambah hidden input per employee
        employeeIds.forEach(id => {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'employee_ids[]';
            input.value = id;
            form.appendChild(input);
        });
    }
    document.getElementById('jobDepartment').addEventListener('change', function() {
        const deptId = this.value;
        if (!deptId) return;
        fetch(`/generate-ticket/${deptId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('jobTicketNumber').value = data.ticket_number;
            });
    });

    function copyTicket() {
        const input = document.getElementById('jobTicketNumber');
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        alert('Ticket number copied!');
    }
    
    document.getElementById("btnConfirmJob")?.addEventListener("click", () => {
        if (!window.currentJobId) return alert("Job belum dipilih");
        fetch(`/jobs/${window.currentJobId}/confirm`, {
                method: "PUT", // karena fetch tidak dukung PUT+form spoof
                headers: {
                    "X-CSRF-TOKEN": '{{ csrf_token() }}',
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    _method: "PUT"
                }),
            })
            .then(res => {
                if (!res.ok) throw new Error("Gagal konfirmasi");
                return res.json();
            })
            .then(() => {
                // alert("Job berhasil dikonfirmasi");
                location.reload();
            })
            .catch(err => alert(err.message));
    });
    document.getElementById("btnRejectJob")?.addEventListener("click", () => {
        if (!window.currentJobId) return alert("Job belum dipilih");
        fetch(`/jobs/${window.currentJobId}/reject`, {
                method: "PUT",
                headers: {
                    "X-CSRF-TOKEN": '{{ csrf_token() }}',
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    _method: "PUT"
                }),
            })
            .then(res => {
                if (!res.ok) throw new Error("Gagal menolak");
                return res.json();
            })
            .then(() => {
                // alert("Job berhasil ditolak");
                location.reload();
            })
            .catch(err => alert(err.message));
    });
</script>
