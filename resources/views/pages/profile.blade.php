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
                                    <li>{{ ucfirst($role->name) }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- NIK (inline edit) -->
                        <div class="mb-4" id="nik-section">
                            <label class="form-label">NIK</label>
                            <div class="d-flex flex-row gap-2">
                                <div class="position-relative flex-grow-1">
                                    <input type="text" id="nikInput" class="form-control"
                                        value="{{ optional(auth()->user()->employee)->nik }}"
                                        placeholder="Cari dengan NIK atau Nama" disabled autocomplete="off">

                                    <!-- kotak suggestion -->
                                    <div id="nikSuggestions" class="list-group position-absolute w-100 shadow"
                                        style="z-index: 1000;"></div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary" type="button" id="editNikBtn">Edit</button>
                                    <button class="btn btn-success d-none" type="button" id="saveNikBtn">Simpan</button>
                                    <button class="btn btn-secondary d-none" type="button" id="cancelNikBtn">Batal</button>
                                </div>
                            </div>

                            <form action="{{ route('profile.update.nik') }}" method="POST" id="nikForm" class="d-none">
                                @csrf @method('PUT')
                                <input type="hidden" name="nik" id="nikHidden">
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
                                <form action="{{ route('profile.update.password') }}" method="POST" id="passwordHiddenForm"
                                    class="d-none">
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
            // ==== NIK inline edit ====
            const allEmployees = @json($employees);

            const nikInput = document.getElementById('nikInput');
            const nikHidden = document.getElementById('nikHidden');
            const editNikBtn = document.getElementById('editNikBtn');
            const saveNikBtn = document.getElementById('saveNikBtn');
            const cancelNikBtn = document.getElementById('cancelNikBtn');
            const nikForm = document.getElementById('nikForm');
            const suggestionBox = document.getElementById('nikSuggestions');

            // === Inline edit ===
            editNikBtn.addEventListener('click', () => {
                nikInput.removeAttribute('disabled');
                editNikBtn.classList.add('d-none');
                saveNikBtn.classList.remove('d-none');
                cancelNikBtn.classList.remove('d-none');
                nikInput.focus();
            });

            cancelNikBtn.addEventListener('click', () => {
                nikInput.value = "{{ optional(auth()->user()->employee)->nik }}";
                nikInput.setAttribute('disabled', true);
                editNikBtn.classList.remove('d-none');
                saveNikBtn.classList.add('d-none');
                cancelNikBtn.classList.add('d-none');
                suggestionBox.innerHTML = '';
            });

            saveNikBtn.addEventListener('click', () => {
                // jangan ambil dari nikInput, tapi dari hidden
                if (!nikHidden.value) {
                    alert('Silakan pilih NIK dari daftar yang tersedia.');
                    return;
                }
                nikForm.submit();
            });

            // === Suggestion ===
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
                        nikInput.value = `${e.nik} - ${e.name}`;
                        nikHidden.value = e.nik; // disimpan hanya NIK
                        suggestionBox.innerHTML = '';
                    });

                    suggestionBox.appendChild(div);
                });
            }

            nikInput.addEventListener('input', () => {
                if (!nikInput.disabled) {
                    showSuggestions(nikInput.value);
                }
            });

            document.addEventListener('click', (e) => {
                if (!nikInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                    suggestionBox.innerHTML = '';
                }
            });

            // ==== PASSWORD toggle ====
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
    </script>
@endsection
