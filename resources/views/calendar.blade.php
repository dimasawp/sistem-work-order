<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kalender Laravel</title>

    {{-- Bootstrap CSS --}}
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    
    {{-- Font Awesome Icon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    {{-- FullCalendar JS --}}
    <script src="{{ asset('js/fullcalendar/main.min.js') }}"></script>
    
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-3">📅 Kalender Aktivitas</h2>

        <form id="sync-form" action="{{ route('employees.sync') }}" method="POST" class="mb-3">
            @csrf
            <button type="submit" id="sync-button" class="btn btn-success d-flex align-items-center">
                <i id="sync-icon" class="fa fa-rotate fa-lg me-2"></i> 
                Sync Karyawan Payroll
                <!-- Icon Info di ujung kanan -->
                <span class="ms-2 border-start" data-bs-toggle="tooltip" data-bs-placement="top" 
                    title="Tekan Sync Karyawan Payroll bila karyawan yang dicari tidak ditemukan.">
                    <i class="fa fa-info fst-italic ms-3 me-1"></i>
                </span>
            </button>
        </form>

        <div id="calendar" class="mb-5"></div>
    </div>

    {{-- Modal Event --}}
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="eventForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah / Edit Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="event_id">

                    {{-- TITLE JOB --}}
                    <div class="mb-3">
                        <label for="title">Judul / Nama Job</label>
                        <input type="text" class="form-control" id="title" required>
                    </div>
                    
                    {{-- PEMBERI JOB --}}
                    <div class="mb-3 position-relative">
                        <label for="task_giver">Pemberi Job</label>
                        <input type="text" id="task_giver" class="form-control" placeholder="Ketik nama / NIK pemberi job">
                        <div id="jobGiverSuggestions" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                    </div>

                    {{-- PENGAMBIL JOB (Dropdown, nanti dynamic dari DB) --}}
                    <div class="mb-3 position-relative">
                        <label for="employee_search" class="form-label">Pengambil Job</label>
                        <input type="text" class="form-control" id="employee_search" autocomplete="off" placeholder="Ketik nama / NIK...">
                        <div id="employeeSuggestions" class="list-group position-absolute w-100" style="z-index: 1000;"></div>

                        <!-- Tempat menaruh chips/banner -->
                        <div id="selectedEmployees" class="mt-2 d-flex flex-wrap gap-2"></div>

                        <!-- Hidden input untuk simpan id karyawan ke form -->
                        <input type="hidden" name="employee_ids[]" id="employee_ids">
                    </div>

                    {{-- ALAT & BAHAN --}}
                    <div class="mb-3">
                        <label for="tools_and_materials">Alat & Bahan</label>
                        <textarea class="form-control" id="tools_and_materials"></textarea>
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="mb-3">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control" id="description"></textarea>
                    </div>

                    {{-- WAKTU MULAI --}}
                    <div class="mb-3">
                        <label for="start_time">Waktu Mulai</label>
                        <input type="datetime-local" class="form-control" id="start_time" required>
                    </div>

                    {{-- WAKTU SELESAI --}}
                    <div class="mb-3">
                        <label for="end_time">Waktu Selesai</label>
                        <input type="datetime-local" class="form-control" id="end_time" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-danger" id="deleteBtn">Hapus</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        document.getElementById('sync-form').addEventListener('submit', function(event) {
            // document.getElementById('sync-icon').classList.add('icon-rotate');
            document.getElementById('sync-icon').classList.add('fa-spin');
            document.getElementById('sync-button').disabled = true;
        });

        // Aktifkan tooltip Bootstrap
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
            const form = document.getElementById('eventForm');
            const deleteBtn = document.getElementById('deleteBtn');

            function formatDateForInput(datetimeStr) {
                const date = new Date(datetimeStr);
                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, '0');
                const dd = String(date.getDate()).padStart(2, '0');
                const hh = String(date.getHours()).padStart(2, '0');
                const min = String(date.getMinutes()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}T${hh}:${min}`;
            }

            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                events: '/api/events',
                eventDisplay: 'block',

                /*Menampilkan waktu atau tidak*/
                // displayEventTime: false,

                //ATAU

                eventTimeFormat: { 
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,multiMonthYear,listMonth',
                },
                buttonText: {
                    today: 'Today',
                    year: 'Year',
                    month: 'Month',
                    list:  'List'
                },
                views: {
                    listMonth: {
                        displayEventTime: true,
                        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false }
                    }
                },

                dateClick: function (info) {
                    form.reset();

                    document.getElementById('title').value = '';
                    document.getElementById('event_id').value = '';
                    document.getElementById('task_giver').value = '';
                    document.getElementById('tools_and_materials').value = '';
                    document.getElementById('description').value = '';
                    document.getElementById('start_time').value = info.dateStr + "T08:00";
                    document.getElementById('end_time').value = info.dateStr + "T17:00";

                    // reset chips employee
                    selectedEmployees = [];
                    updateHiddenInput();
                    renderSelectedEmployees();

                    deleteBtn.style.display = 'none';
                    modal.show();
                },

                eventClick: function (info) {
                    const event = info.event;
                    const data = event.extendedProps;

                    document.getElementById('event_id').value = event.id;
                    document.getElementById('title').value = event.title || '';
                    document.getElementById('task_giver').value = data.task_giver || '';
                    document.getElementById('tools_and_materials').value = data.tools_and_materials || '';
                    document.getElementById('description').value = data.description || '';
                    document.getElementById('start_time').value = formatDateForInput(event.startStr);
                    document.getElementById('end_time').value = formatDateForInput(event.endStr);

                    // isi ulang chips employee
                    selectedEmployees = data.employees || [];
                    updateHiddenInput();
                    renderSelectedEmployees();

                    deleteBtn.style.display = 'inline-block';
                    modal.show();
                },

                eventDidMount: function(info) {
                    let giver = info.event.extendedProps.task_giver || '-';

                    // Ambil hanya nama setelah " - "
                    if (giver.includes(' - ')) {
                        giver = giver.split(' - ')[1];
                    }

                    const tooltip = new bootstrap.Tooltip(info.el, {
                        title: `<div style="white-space: pre-line;">
                            ${info.event.title}
                             | ${giver || '-'}
                             | ${new Date(info.event.start).toLocaleString('id-ID', {
                                day: '2-digit', month: '2-digit', year: 'numeric',
                                hour: '2-digit', minute: '2-digit'
                            })}
                        </div>`,
                        placement: 'top',
                        trigger: 'hover',
                        html: true,
                        container: 'body'
                    });
                }
            });

            calendar.render();

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const id = document.getElementById('event_id').value;
                const method = id ? 'PUT' : 'POST';
                const url = id ? `/api/events/${id}` : '/api/events';

                const payload = {
                    title: document.getElementById('title').value,
                    employee_ids: JSON.parse(document.getElementById('employee_ids').value || "[]"),
                    task_giver: document.getElementById('task_giver').value,
                    tools_and_materials: document.getElementById('tools_and_materials').value,
                    description: document.getElementById('description').value,
                    start_time: document.getElementById('start_time').value,
                    end_time: document.getElementById('end_time').value
                };

                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.ok ? res.json().catch(() => ({})) : Promise.reject(res))
                .then(() => {
                    modal.hide();
                    calendar.refetchEvents();
                })
                .catch(err => console.error("Save error:", err));
            });

            deleteBtn.addEventListener('click', function () {
                const id = document.getElementById('event_id').value;
                if (!id) return;

                fetch(`/api/events/${id}`, {
                    method: 'DELETE'
                })
                .then(res => res.ok ? res.json().catch(() => ({})) : Promise.reject(res))
                .then(() => {
                    modal.hide();
                    calendar.refetchEvents();
                })
                .catch(err => console.error("Delete error:", err));
            });
        });
    </script>

    <script>
        const allKaryawan = @json($allKaryawan);
        const jobInput = document.getElementById('task_giver');
        const jobSuggestions = document.getElementById('jobGiverSuggestions');

        function showJobSuggestions(keyword) {
            jobSuggestions.innerHTML = '';
            if (!keyword) return;

            const filtered = allKaryawan.filter(k =>
                k.nik.includes(keyword) ||
                (k.enroll_id && k.enroll_id.includes(keyword)) ||
                k.name.toLowerCase().includes(keyword.toLowerCase())
            );

            filtered.forEach(k => {
                const div = document.createElement('div');
                div.classList.add('list-group-item', 'list-group-item-action');
                div.textContent = `${k.nik} - ${k.name} (${k.kd_bagian || '-'})`;
                div.addEventListener('click', () => {
                    jobInput.value = `${k.nik} - ${k.name}`;
                    jobSuggestions.innerHTML = '';
                });
                jobSuggestions.appendChild(div);
            });
        }

        jobInput.addEventListener('input', () => showJobSuggestions(jobInput.value));
        document.addEventListener('click', e => {
            if (!jobSuggestions.contains(e.target) && e.target !== jobInput) {
                jobSuggestions.innerHTML = '';
            }
        });
    </script>

    <script>
        const allEmployees = @json($teknisiListrik); 
        // data berisi {id, nik, name, kd_bagian}

        const searchInput = document.getElementById('employee_search');
        const suggestionBox = document.getElementById('employeeSuggestions');
        const selectedBox = document.getElementById('selectedEmployees');
        const hiddenInput = document.getElementById('employee_ids');

        let selectedEmployees = [];

        function renderSelectedEmployees() {
            selectedBox.innerHTML = '';
            selectedEmployees.forEach(emp => {
                const badge = document.createElement('span');
                badge.classList.add('badge', 'bg-primary', 'd-flex', 'align-items-center');
                badge.style.gap = '6px';
                badge.innerHTML = `${emp.nik} - ${emp.name} 
                    <button type="button" class="btn-close btn-close-white btn-sm ms-1" aria-label="Remove"></button>`;
                
                badge.querySelector('button').addEventListener('click', () => {
                    selectedEmployees = selectedEmployees.filter(e => e.id !== emp.id);
                    updateHiddenInput();
                    renderSelectedEmployees();
                });

                selectedBox.appendChild(badge);
            });
        }

        function updateHiddenInput() {
            hiddenInput.value = JSON.stringify(selectedEmployees.map(e => e.id));
        }

        function showSuggestions(keyword) {
            suggestionBox.innerHTML = '';
            if (!keyword) return;

            const filtered = allEmployees.filter(e =>
                e.nik.includes(keyword) || e.name.toLowerCase().includes(keyword.toLowerCase())
            );

            filtered.forEach(e => {
                const div = document.createElement('div');
                div.classList.add('list-group-item', 'list-group-item-action');
                div.textContent = `${e.nik} - ${e.name}`;

                div.addEventListener('click', () => {
                    if (!selectedEmployees.find(emp => emp.id === e.id)) {
                        selectedEmployees.push(e);
                        updateHiddenInput();
                        renderSelectedEmployees();
                    }
                    searchInput.value = '';
                    suggestionBox.innerHTML = '';
                });

                suggestionBox.appendChild(div);
            });
        }

        searchInput.addEventListener('input', () => {
            showSuggestions(searchInput.value);
        });

        document.addEventListener('click', function (e) {
            if (!suggestionBox.contains(e.target) && e.target !== searchInput) {
                suggestionBox.innerHTML = '';
            }
        });
    </script>
</body>
</html>
