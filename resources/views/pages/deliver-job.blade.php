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
                            
                            <div class="d-flex flex-row gap-2 text-white">
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
                            <span class="badge {{ $job->status == 'pending' ? 'bg-secondary' : ($job->status == 'on_process'? 'bg-warning' : 'bg-danger') }}">{{ $job->status }}</span>
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
                <tr class="text-center">
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
                    <td class="text-center"><span class="badge {{ $job->status == 'pending' ? 'bg-secondary' : ($job->status == 'on_process'? 'bg-warning' : 'bg-danger') }}">{{ $job->status }}</span></td>
                    <td class="d-flex flex-row justify-content-center gap-2">
                        <!-- Edit Button -->
                        <button class="btn btn-sm btn-warning" onclick='openEditJobModal(@json($job))'>
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
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL JOB COMPONENT --}}
<x-job-modal :departments="$departments" mode="giver"/>

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
</script>
@endsection
