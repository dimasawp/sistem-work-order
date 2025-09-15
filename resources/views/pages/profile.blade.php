@extends('layouts.app')

@section('title', 'Profile')
@section('page-name', 'Profile')

@section('content')
    <div class="p-4">
        <h3 class="mb-4">Profile</h3>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <!-- Username (readonly) -->
                        <div class="mb-4">
                            <label class="form-label">Username</label>
                            <input type="text" readonly class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                value="{{ auth()->user()->username }}">
                        </div>

                        <!-- Department (readonly) -->
                        <div class="mb-4">
                            <label class="form-label">Department</label>
                            <input type="text" readonly class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                value="{{ auth()->user()->department->name }}">
                        </div>

                        <!-- Role (list) -->
                        <div class="mb-4">
                            <label class="form-label">Role Akses</label>
                            <ul>
                                @foreach (auth()->user()->roles as $role)
                                    <li class="">
                                        {{ ucfirst($role->name) }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Email (inline edit) -->
                        <div class="mb-4" id="email-section">
                            <label class="form-label">Email</label>
                            <div class="d-flex gap-2 align-items-center">
                                <!-- <input type="email" id="emailInput" class="form-control" value="{{ auth()->user()->email }}" readonly> -->
                                <input type="email" id="emailInput" class="form-control"
                                    value="{{ auth()->user()->email }}" disabled>
                                <button class="btn btn-outline-primary" type="button" id="editEmailBtn">Edit</button>
                                <button class="btn btn-success d-none" type="button" id="saveEmailBtn">Simpan</button>
                                <button class="btn btn-secondary d-none" type="button" id="cancelEmailBtn">Batal</button>
                            </div>
                            {{-- <form action="{{ route('profile.update.email') }}" method="POST" id="emailForm" class="d-none"> --}}
                            <form action="" method="POST" id="emailForm" class="d-none">
                                @csrf @method('PUT')
                                <input type="hidden" name="email" id="emailHidden">
                            </form>
                        </div>

                        <!-- Ubah Password -->
                        <div class="mb-3" id="password-section">
                            <label class="form-label d-block">Password</label>
                            <button class="btn btn-outline-warning mb-2" type="button" id="showPasswordForm">
                                <i class="fa-solid fa-key me-2"></i> Ubah Password
                            </button>

                            <div id="passwordForm" class="d-none">
                                <div class="d-flex flex-column gap-2">
                                    <input type="password" class="form-control mt-2" id="newPassword"
                                        placeholder="Password Baru">
                                    <input type="password" class="form-control mt-2" id="confirmPassword"
                                        placeholder="Konfirmasi Password">
                                    <div class="d-flex justify-content-end gap-2 mt-2">
                                        <button class="btn btn-success" type="button" id="savePasswordBtn">Simpan</button>
                                        <button class="btn btn-secondary" type="button"
                                            id="cancelPasswordBtn">Batal</button>
                                    </div>
                                </div>
                                <form action="{{ route('profile.update.password') }}" method="POST" id="passwordHiddenForm" class="d-none">
                                {{-- <form action="" method="POST" id="passwordHiddenForm" class="d-none"> --}}
                                    @csrf @method('PUT')
                                    <input type="hidden" name="password" id="passwordHidden">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // EMAIL inline edit
            const emailInput = document.getElementById('emailInput');
            const emailHidden = document.getElementById('emailHidden');
            const editEmailBtn = document.getElementById('editEmailBtn');
            const saveEmailBtn = document.getElementById('saveEmailBtn');
            const cancelEmailBtn = document.getElementById('cancelEmailBtn');
            const emailForm = document.getElementById('emailForm');

            editEmailBtn.addEventListener('click', () => {
                // emailInput.removeAttribute('readonly');
                emailInput.removeAttribute('disabled');
                editEmailBtn.classList.add('d-none');
                saveEmailBtn.classList.remove('d-none');
                cancelEmailBtn.classList.remove('d-none');
                emailInput.focus();
            });

            cancelEmailBtn.addEventListener('click', () => {
                emailInput.value = "{{ auth()->user()->email }}";
                emailInput.setAttribute('readonly', true);
                editEmailBtn.classList.remove('d-none');
                saveEmailBtn.classList.add('d-none');
                cancelEmailBtn.classList.add('d-none');
            });

            saveEmailBtn.addEventListener('click', () => {
                emailHidden.value = emailInput.value;
                emailForm.submit();
            });

            // PASSWORD form toggle
            const showPasswordForm = document.getElementById('showPasswordForm');
            const passwordForm = document.getElementById('passwordForm');
            const savePasswordBtn = document.getElementById('savePasswordBtn');
            const cancelPasswordBtn = document.getElementById('cancelPasswordBtn');
            const passwordHiddenForm = document.getElementById('passwordHiddenForm');
            const passwordHidden = document.getElementById('passwordHidden');

            showPasswordForm.addEventListener('click', () => {
                passwordForm.classList.remove('d-none');
                showPasswordForm.classList.add('d-none');
            });

            cancelPasswordBtn.addEventListener('click', () => {
                passwordForm.classList.add('d-none');
                showPasswordForm.classList.remove('d-none');
            });

            savePasswordBtn.addEventListener('click', () => {
                if (document.getElementById('newPassword').value === document.getElementById(
                        'confirmPassword').value) {
                    passwordHidden.value = document.getElementById('newPassword').value;
                    passwordHiddenForm.submit();
                } else {
                    alert('Password tidak cocok!');
                }
            });
        });

        document.getElementById('saveEmailBtn')?.addEventListener('click', function() {
            const email = document.getElementById('emailInput').value;
            document.getElementById('emailHidden').value = email;
            document.getElementById('emailForm').submit();
        });

        document.getElementById('savePasswordBtn')?.addEventListener('click', function() {
            const pass = document.getElementById('newPassword').value;
            const confirm = document.getElementById('confirmPassword').value;

            if (pass !== confirm) {
                alert('Password tidak sama!');
                return;
            }

            alert('halo')

            document.getElementById('passwordHidden').value = pass;
            document.getElementById('passwordConfirmationHidden').value = confirm;
            document.getElementById('passwordHiddenForm').submit();
        });
    </script>

@endsection
