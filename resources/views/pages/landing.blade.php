<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Jobs Landing</title>

    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/job-modal.js') }}"></script>

    <style>
        body {
            background: #f5f7fa;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row" style="min-height: 100vh;">

            <div class="col-md-8 p-4 d-flex flex-column">
                <div class="py-2 mb-4">
                    <h1>Work Order</h1>
                </div>

                <!-- Form Pencarian -->
                <div class="mb-4">
                    <form method="GET" action="{{ route('jobs.search') }}" class="d-flex w-100" style="height: 3rem;">
                        <input type="text" name="q" class="form-control me-2 flex-grow-1"
                            placeholder="Masukkan nomor ticket untuk cek job..." value="{{ request('q') }}">
                        <button class="btn btn-primary px-4 fw-semibold text-nowrap" type="submit">
                            Cek Job
                        </button>
                    </form>
                </div>

                <!-- Daftar Job -->
                <div class="row g-3">
                    @forelse($jobs as $job)
                        <div class="col-md-4">
                            <div class="card shadow-sm h-100 position-relative job-card" data-bs-toggle="modal" data-bs-target="#jobModal" data-job='@json($job)'>
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div>
                                            <h5 class="card-title mb-0">{{ $job->title }}</h5>
                                            <small class="text-muted">{{ $job->ticket_number }}</small>
                                        </div>
                                        <div>
                                            <span
                                                class="badge {{ $job->status === 'pending' ? 'bg-secondary' : ($job->status === 'on_process' ? 'bg-warning text-dark' : 'bg-success') }}">
                                                {{ ucfirst($job->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <span class="badge bg-primary align-self-start mb-2">
                                        {{ $job->department->name }}
                                    </span>
                                    <p class="text-secondary small flex-grow-1">
                                        {{ Str::limit($job->description, 80) }}
                                    </p>

                                    <div class="d-flex justify-content-end align-items-center mt-auto">
                                        <p class="small text-muted mb-0">
                                            {{ $job->created_at->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Tidak ada job.</p>
                    @endforelse
                </div>
            </div>

            <!-- Kolom Login -->
            <div class="col-md-4 p-4 d-flex flex-column justify-content-center align-items-center bg-dark text-light">
                <div class="w-75">
                    <h2 class="mb-3 text-center">Login</h2>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            Login
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- Komponen Modal Job --}}
    <x-job-modal :departments="$departments ?? []" mode="view" />

    {{-- Script --}}
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.job-card').forEach(card => {
                card.addEventListener('click', () => {
                    const job = JSON.parse(card.dataset.job);
                    openJobModal(job);
                });
            });
        });
    </script>
</body>

</html>
