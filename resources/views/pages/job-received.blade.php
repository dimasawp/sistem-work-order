@extends('layouts.app')

{{-- @section('title', 'Job Received') --}}
@section('title', 'Job Masuk')
{{-- @section('page-name', 'Job Received') --}}
@section('page-name', 'Job Masuk')

@section('content')
    <div class="p-4">
        {{-- <h3 class="mb-3">Job Received</h3> --}}
        <h3 class="mb-3">Job Masuk</h3>

        <div class="d-flex flex-row gap-4" style="height: 80vh">
            <div class="w-35" style="flex: 0 0 25%; overflow-y: auto;">
                <h6 class="text-secondary mt-2 mb-3">Unassign Jobs/On Process Jobs</h6>
                <ul class="list-group" id="jobList">
                    @forelse($receivedJobs as $job)
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            data-event='@json(['id' => $job->id, 'title' => $job->title, 'job' => $job])'>
                            <div>
                                <div class="d-flex flex-column">
                                    <div>
                                        <strong>{{ $job->title }}</strong> -
                                        <span
                                            class="badge {{ $job->status == 'pending' ? 'bg-secondary' : ($job->status == 'on_process' ? 'bg-warning' : 'bg-danger') }}">{{ $job->status }}</span>
                                    </div>
                                    <small class="text-muted">{{ $job->ticket_number }}</small>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-warning text-white"
                                onclick='openEditJobModal(@json($job))'>
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Belum ada job diterima.</li>
                    @endforelse
                </ul>
            </div>

            <div id="calendar" style="flex: 1; background: #fff;" class="rounded border p-2"></div>
        </div>
    </div>

    {{-- MODAL JOB COMPONENT --}}
    <x-job-modal :departments="$departments" mode="receiver" />

    {{-- POP UP Drop --}}
    <div class="modal fade" id="actionConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Konfirmasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="actionConfirmMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmActionBtn">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 1. Buat draggable list items
        var jobEls = document.querySelectorAll('#jobList li');
        jobEls.forEach(function(el) {
            new FullCalendar.Draggable(el, {
                eventData: function() {
                    return JSON.parse(el.dataset.event);
                }
            });
        });

        function showConfirm(message, onConfirm, onCancel) {
            const msgEl = document.getElementById('actionConfirmMessage');
            const modalEl = document.getElementById('actionConfirmModal');
            const confirmBtn = document.getElementById('confirmActionBtn');

            msgEl.textContent = message;

            const modal = new bootstrap.Modal(modalEl);

            // Bersihkan handler lama biar gak numpuk
            confirmBtn.onclick = null;
            modalEl.onhidden = null;

            // Klik tombol Confirm
            confirmBtn.onclick = () => {
                modal.hide();
                onConfirm && onConfirm();
            };

            // Jika user menutup modal tanpa klik confirm
            modalEl.addEventListener('hidden.bs.modal', function handler() {
                modalEl.removeEventListener('hidden.bs.modal', handler);
                if (onCancel) onCancel();
            });

            modal.show();
        }

        function formatLocal(date) {
            return date.getFullYear() + '-' +
                String(date.getMonth() + 1).padStart(2, '0') + '-' +
                String(date.getDate()).padStart(2, '0') + 'T' +
                String(date.getHours()).padStart(2, '0') + ':' +
                String(date.getMinutes()).padStart(2, '0') + ':' +
                String(date.getSeconds()).padStart(2, '0');
        }

        // 2. Inisialisasi FullCalendar
        document.addEventListener('DOMContentLoaded', function() {
            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                editable: true,
                droppable: true,
                events: @json($assignedJobs),
                eventDisplay: 'block',
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
                    list: 'List'
                },
                views: {
                    listMonth: {
                        displayEventTime: true,
                        eventTimeFormat: {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        }
                    }
                },
                eventClick: function(info) {
                    // kalau job lengkap sudah dipass dari server
                    let job = info.event.extendedProps.job;
                    // console.log(job)
                    if (job) {
                        // console.log(job.status);
                        openEditJobModal(job);
                    } else {
                        alert("Job data tidak lengkap.");
                    }
                },
                // eventReceive: function(info) {
                //     let jobId = info.event.id;
                //     let start = new Date(info.event.start);
                //     start.setHours(8, 0, 0); // jam 08:00 WIB

                //     let startLocal = formatLocal(start);

                //     if (!confirm("Apakah kamu yakin ingin menempatkan job ini di tanggal " +
                //             startLocal + "?")) {
                //         info.revert();
                //         return;
                //     }

                //     fetch(`/api/jobs/${jobId}/update-time`, {
                //             method: 'POST',
                //             headers: {
                //                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                //                 'Content-Type': 'application/json'
                //             },
                //             body: JSON.stringify({
                //                 start_time: startLocal,
                //                 end_time: null
                //             })
                //         })
                //         .then(res => res.json())
                //         .then(data => {
                //             if (!data.success) {
                //                 alert("Gagal update job!");
                //                 info.revert();
                //             } else {
                //                 let ev = info.event;
                //                 ev.setAllDay(false);
                //                 ev.setStart(
                //                 start); // set object Date langsung supaya bubble tampil jam
                //                 ev.setEnd(null);

                //                 if (ev.extendedProps.job) {
                //                     ev.extendedProps.job.start_time = startLocal;
                //                     ev.extendedProps.job.end_time = null;
                //                 }
                //             }
                //         })
                //         .catch(() => info.revert());
                // },
                // eventDrop: function(info) {
                //     let jobId = info.event.id;

                //     let start = new Date(info.event.start);
                //     start.setHours(8, 0, 0);

                //     let end = info.event.end ? new Date(info.event.end) : null;

                //     if (!confirm("Apakah kamu yakin ingin memindahkan job ini ke tanggal " + start
                //             .toISOString() + "?")) {
                //         info.revert();
                //         return;
                //     }
                //     let startLocal = formatLocal(start);
                //     let endLocal = end ? formatLocal(end) : null;

                //     fetch(`/api/jobs/${jobId}/update-time`, {
                //             method: 'POST',
                //             headers: {
                //                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                //                 'Content-Type': 'application/json'
                //             },
                //             body: JSON.stringify({
                //                 start_time: startLocal,
                //                 end_time: endLocal
                //             })
                //         })
                //         .then(res => res.json())
                //         .then(data => {
                //             // console.log('Response dari server:', data); // <-- Tambahkan log
                //             // console.log("start", start, "end", end);

                //             if (!data.success) {
                //                 alert("Gagal update job!");
                //                 info.revert(); // kembali ke posisi awal
                //             } else {
                //                 let ev = info.event;
                //                 if (ev.extendedProps.job) {
                //                     ev.extendedProps.job.start_time = startLocal; // <-- string
                //                     ev.extendedProps.job.end_time = end ? formatLocal(end) : null;
                //                 }

                //                 ev.setStart(start);
                //                 ev.setEnd(end);
                //             }
                //         })
                //         .catch((err) => {
                //             console.error('Error fetch:', err);
                //             info.revert();
                //         });
                // }

                eventReceive: function(info) {
                    const job = info.event.extendedProps.job;
                    const jobId = info.event.id;
                    const start = new Date(info.event.start);
                    start.setHours(8, 0, 0);
                    const startLocal = formatLocal(start);

                    // Hapus bubble sementara dari kalender
                    info.event.remove();

                    showConfirm(
                        `Apakah kamu yakin ingin menempatkan job ini di tanggal ${startLocal}?`,
                        () => { // ✅ onConfirm
                            fetch(`/api/jobs/${jobId}/update-time`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        start_time: startLocal,
                                        end_time: null,
                                        status: 'on_process'
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (!data.success) {
                                        alert("Gagal update job!");
                                    } else {
                                        // ✅ Update juga data job agar modal bisa baca tanggal baru
                                        if (job) {
                                            job.start_time = startLocal;
                                            job.end_time = null;
                                            job.status = 'on_process';

                                            // Cari <li> berdasarkan ID job
                                            const listItem = document.querySelector(
                                                `#jobList li[data-event*='"id":${jobId}']`
                                            );

                                            if (listItem) {
                                                // Cari elemen badge status di dalam <li>
                                                const badge = listItem.querySelector('.badge');
                                                if (badge) {
                                                    badge.textContent = 'on_process';
                                                    badge.classList.remove('bg-secondary',
                                                        'bg-danger');
                                                    badge.classList.add('bg-warning');
                                                }

                                                // ✅ Kalau mau simpan status baru di atribut data-event juga:
                                                const eventData = JSON.parse(listItem.dataset
                                                    .event);
                                                eventData.job.status = 'on_process';
                                                listItem.dataset.event = JSON.stringify(
                                                    eventData);
                                            }

                                        }

                                        // Tambahkan event kembali ke kalender
                                        info.view.calendar.addEvent({
                                            id: jobId,
                                            title: info.event.title,
                                            start: start, // objek Date
                                            allDay: false,
                                            extendedProps: {
                                                job
                                            } // sudah berisi start_time baru
                                        });
                                    }
                                })
                                .catch(() => {
                                    alert("Terjadi kesalahan saat menyimpan.");
                                });
                        },
                        () => {
                            // ❌ batal: tidak lakukan apa-apa
                        }
                    );
                },
                eventDrop: function(info) {
                    const jobId = info.event.id;
                    const newStart = new Date(info.event.start);
                    newStart.setHours(8, 0, 0);
                    const newEnd = info.event.end ? new Date(info.event.end) : null;

                    const startLocal = formatLocal(newStart);
                    const endLocal = newEnd ? formatLocal(newEnd) : null;

                    // Simpan data event sebelum di-revert
                    const originalEvent = info.event;
                    const eventData = {
                        id: originalEvent.id,
                        title: originalEvent.title,
                        extendedProps: originalEvent.extendedProps
                    };

                    // Revert dulu supaya bubble "drop sementara" hilang
                    info.revert();

                    showConfirm(
                        `Apakah kamu yakin ingin memindahkan job ini ke tanggal ${startLocal}?`,
                        () => {
                            fetch(`/api/jobs/${jobId}/update-time`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        start_time: startLocal,
                                        end_time: endLocal
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (!data.success) {
                                        alert("Gagal update job!");
                                        return;
                                    }

                                    // Hapus event lama kalau masih ada
                                    const existing = info.view.calendar.getEventById(jobId);
                                    if (existing) existing.remove();

                                    // Buat bubble baru di tanggal baru
                                    const updatedJob = {
                                        ...eventData.extendedProps.job,
                                        start_time: startLocal,
                                        end_time: endLocal,
                                        status: 'on_process'
                                    };

                                    info.view.calendar.addEvent({
                                        id: jobId,
                                        title: eventData.title,
                                        start: newStart,
                                        end: newEnd,
                                        allDay: false,
                                        extendedProps: {
                                            job: updatedJob
                                        }
                                    });
                                })
                                .catch(() => alert("Terjadi kesalahan saat update."));
                        },
                        () => {
                            // batal -> tidak lakukan apa pun (bubble lama tetap ada karena revert)
                        }
                    );
                },
            });
            calendar.render();
        });
    </script>
    <script>
        const allEmployees = @json($employeesForJs ?? []);

        const searchInput = document.getElementById('employee_search');
        const suggestionBox = document.getElementById('employeeSuggestions');
        const selectedBox = document.getElementById('selectedEmployees');
        const form = document.getElementById('jobForm'); // ambil form biar bisa inject input

        let selectedEmployees = [];

        // Render chip + hidden inputs
        function renderSelectedEmployees() {
            selectedBox.innerHTML = '';

            // Hapus semua hidden input lama
            form.querySelectorAll('input[name="employee_ids[]"]').forEach(el => el.remove());

            selectedEmployees.forEach(emp => {
                // buat chip
                const badge = document.createElement('span');
                badge.classList.add('badge', 'bg-primary', 'd-flex', 'align-items-center');
                badge.style.gap = '6px';
                badge.innerHTML = `${emp.nik} - ${emp.name} 
            <button type="button" class="btn-close btn-close-white btn-sm ms-1" aria-label="Remove"></button>`;

                // hapus employee
                badge.querySelector('button').addEventListener('click', () => {
                    selectedEmployees = selectedEmployees.filter(e => e.id !== emp.id);
                    renderSelectedEmployees(); // refresh
                });

                selectedBox.appendChild(badge);

                // buat hidden input per ID
                const hidden = document.createElement('input');
                hidden.type = "hidden";
                hidden.name = "employee_ids[]";
                hidden.value = emp.id;
                form.appendChild(hidden);
            });
        }

        // Suggestion box
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

        document.addEventListener('click', function(e) {
            if (!suggestionBox.contains(e.target) && e.target !== searchInput) {
                suggestionBox.innerHTML = '';
            }
        });
    </script>
    <script>
        window.jobs = @json($jobs);
        const jobs = @json($jobs);
    </script>
@endsection
