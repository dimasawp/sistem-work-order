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
        .search-box {
            max-width: 600px;
        }
        .job-card {
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }
        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        }
        .sticky-login {
            position: sticky;
            top: 2rem;
        }
    </style>
</head>

<body>
    <div class="container-fluid px-4 pt-3 pb-1 bg-dark text-light">
        <h2 class="text-white">GAWE'!</h2>
    </div>

    {{-- Hero Search --}}
    <section class="py-5 text-center search-section bg-dark text-light">
        <div class="container">

            <h1 class="fw-bold mb-3">Cari Job</h1>
            <form method="GET" action="{{ route('jobs.search') }}" class="d-flex mx-auto search-box">
                <input type="text" name="q" class="form-control form-control-lg me-2"
                    placeholder="Cari berdasarkan judul job..." value="{{ request('q') }}">
                <button class="btn btn-light btn-lg" type="submit">Cari</button>
            </form>
        </div>
    </section>

    <div class="container py-5">
        <div class="row">
            {{-- Card Jobs --}}
            <div class="col-lg-8">
                <div class="row g-4">
                    @forelse($jobs as $job)
                        <div class="col-md-6">
                            <div class="card job-card shadow-sm h-100" data-bs-toggle="modal" data-bs-target="#jobModal"
                                data-job='@json($job)'>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $job->title }}</h5>
                                    <span class="badge bg-primary w-fit mb-2">{{ $job->department->name }}</span>
                                    <p class="text-secondary small flex-grow-1">{{ Str::limit($job->description, 80) }}
                                    </p>
                                    <p class="small text-muted text-end mb-0">{{ $job->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Tidak ada job ditemukan.</p>
                    @endforelse
                </div>
            </div>

            {{-- Login Form --}}
            <div class="col-lg-4">
                <div class="card p-4 shadow-sm sticky-login">
                    <h4 class="mb-3 text-center">Login</h4>
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
                            <input type="text" name="username" class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>

                    <a href=""></a>
                </div>
            </div>
        </div>
    </div>

    {{-- Komponen Modal Job --}}
    <x-job-modal :departments="$departments" mode="view" />

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
