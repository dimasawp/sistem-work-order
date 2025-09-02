@extends('layouts.app')

@section('title', 'Employee List')
@section('page-name', 'Employee List')

@section('content')
    <div class="p-4">
        <h3 class="mb-3">Employee List</h3>

        <div class="mb-3">
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
        </div>

        {{-- <table class="table table-bordered">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Enroll ID</th>
                    <th>Nama</th>
                    <th>Sub Department</th>
                    <th>Posisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    <tr>
                        <td>{{ $employee->nik }}</td>
                        <td>{{ $employee->enroll_id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->subDepartment->name ?? '-' }}</td>
                        <td>{{ $employee->position }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data karyawan</td>
                    </tr>
                @endforelse
            </tbody>
        </table> --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Enroll ID</th>
                    <th>Nama</th>
                    <th>Sub Department</th>
                    <th>Posisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    <tr>
                        <td>{{ $employee->nik }}</td>
                        <td>{{ $employee->enroll_id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->subDepartment->name ?? '-' }}</td>
                        <td>{{ $employee->position }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data karyawan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $employees->links('pagination::bootstrap-4') }}
        </div>

    </div>

    <script>
        document.getElementById('sync-form').addEventListener('submit', function(event) {
            // document.getElementById('sync-icon').classList.add('icon-rotate');
            document.getElementById('sync-icon').classList.add('fa-spin');
            document.getElementById('sync-button').disabled = true;
        });

        // Aktifkan tooltip Bootstrap
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endsection
