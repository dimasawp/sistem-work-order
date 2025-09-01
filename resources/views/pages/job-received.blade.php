@extends('layouts.app')

@section('title', 'Job Received')

@section('content')
    <div class="p-4">
        <h3 class="mb-3">Job Received</h3>

        <div class="d-flex flex-row gap-4" style="height: 80vh">
            <div class="w-35" style="flex: 0 0 25%; overflow-y: auto;">
                <h6 class="text-secondary mt-2 mb-3">Unassign Jobs/New Jobs</h6>
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

        {{-- Bagian tabel job history. --}}
        <div class="mt-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Job History</h5>
                <button class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Pemberi Job</th>
                        <th>Status</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobHistory as $job)
                        <tr>
                            <td>{{ $job->title }}</td>
                            <td>{{ $job->description }}</td>
                            <td>{{ $job->giver->name ?? '-' }}</td>
                            <td>{{ ucfirst($job->status) }}</td>
                            <td>{{ $job->start_time }}</td>
                            <td>{{ $job->end_time }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center">Belum ada job selesai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                    list:  'List'
                },
                views: {
                    listMonth: {
                        displayEventTime: true,
                        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false }
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

                    if (!confirm("Apakah kamu yakin ingin menempatkan job ini di tanggal " + start + "?")) {
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

                    if (!confirm("Apakah kamu yakin ingin memindahkan job ini ke tanggal " + start + "?")) {
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
@endsection
