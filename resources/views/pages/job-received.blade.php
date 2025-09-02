@extends('layouts.app')

@section('title', 'Job Received')
@section('page-name', 'Job Received')

@section('content')
    <div class="p-4">
        <h3 class="mb-3">Job Received</h3>

        <div class="d-flex flex-row gap-4" style="height: 80vh">
            <div class="w-35" style="flex: 0 0 25%; overflow-y: auto;">
                <h6 class="text-secondary mt-2 mb-3">Unassign Jobs/On Process Jobs</h6>
                <ul class="list-group" id="jobList">
                    @forelse($receivedJobs as $job)
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            data-event='@json(['id' => $job->id, 'title' => $job->title, 'job' => $job])'>
                            <span>
                                <strong>{{ $job->title }}</strong> - {{ $job->status }}
                            </span>
                            <button class="btn btn-sm btn-warning" onclick='openEditJobModal(@json($job))'>
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

        // 2. Inisialisasi FullCalendar
        document.addEventListener('DOMContentLoaded', function() {
            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                editable: true,
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
                    if (job) {
                        // console.log(job.status);
                        openEditJobModal(job);
                    } else {
                        alert("Job data tidak lengkap.");
                    }
                },
                eventReceive: function(info) {
                    let jobId = info.event.id;
                    let start = info.event.startStr; // pakai startStr biar tidak geser timezone

                    if (!confirm("Apakah kamu yakin ingin menempatkan job ini di tanggal " + start +
                            "?")) {
                        info.revert();
                        return;
                    }

                    fetch(`/api/jobs/${jobId}/update-time`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                start_time: start,
                                end_time: null
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) {
                                alert("Gagal update job!");
                                info.revert();
                            } else {
                                calendar.refetchEvents(); // refresh event biar langsung muncul
                            }
                        })
                        .catch(() => info.revert());
                },
                eventDrop: function(info) {
                    let jobId = info.event.id;
                    let start = info.event.startStr;
                    let end = info.event.endStr;

                    if (!confirm("Apakah kamu yakin ingin memindahkan job ini ke tanggal " + start +
                            "?")) {
                        info.revert();
                        return;
                    }

                    fetch(`/api/jobs/${jobId}/update-time`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                start_time: start,
                                end_time: end
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) {
                                alert("Gagal update job!");
                                info.revert();
                            } else {
                                calendar.refetchEvents(); // refresh event biar langsung muncul
                            }
                        })
                        .catch(() => info.revert());
                }
            });
            calendar.render();
        });
    </script>
    <script>
        const allEmployees = @json($employeesForJs ?? []);
        console.log(allEmployees);
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

        document.addEventListener('click', function(e) {
            if (!suggestionBox.contains(e.target) && e.target !== searchInput) {
                suggestionBox.innerHTML = '';
            }
        });
    </script>

@endsection
