@extends('layouts.app')

@section('title', 'Job Received')

@section('content')
<div class="p-4">
    <h3 class="mb-3">Job Received</h3>

    {{-- <div class="d-flex flex-row justify-content-between gap-4" style="height: 80vh">
        <div class="w-25">
            <ul class="list-group mt-4">
                @forelse($receivedJobs as $job)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <strong>{{ $job->title }}</strong> - {{ $job->status }}
                        </span>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#jobModal{{ $job->id }}">
                            Lihat / Edit
                        </button>
                    </li>

                @empty
                    <li class="list-group-item text-muted">Belum ada job diterima.</li>
                @endforelse
            </ul>
        </div>
        <div id="calendar" class="w-100"></div>
    </div> --}}
    <div class="d-flex flex-row gap-4" style="height: 80vh">
    <div class="w-35" style="flex: 0 0 25%; overflow-y: auto;">
        <ul class="list-group mt-4" id="jobList">
            @forelse($receivedJobs as $job)
                <li class="list-group-item d-flex justify-content-between align-items-center" 
                    data-event='{"id": "{{ $job->id }}", "title": "{{ $job->title }}"}'>
                    <span>
                        <strong>{{ $job->title }}</strong> - {{ $job->status }}
                    </span>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#jobModal{{ $job->id }}">
                        Lihat / Edit
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
            eventDrop: function(info) {
                let jobId = info.event.id;
                let start = info.event.start.toISOString();
                let end = info.event.end ? info.event.end.toISOString() : null;

                fetch(`/jobs/${jobId}/update-time`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ start_time: start, end_time: end })
                });
            }
        });
        calendar.render();
    });
</script>
@endsection
