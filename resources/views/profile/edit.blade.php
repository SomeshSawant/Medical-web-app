<x-app-layout>
    <x-slot name="header">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Account Profile</li>
                </ol>
            </nav>
            <h1 class="h4 fw-bold text-dark mb-1">User Account Profile</h1>
            <p class="text-muted small mb-0">Manage your clinical system identity, credentials, and session settings</p>
        </div>
    </x-slot>

    <div class="row g-4 justify-content-center">
        <!-- Update Profile Information -->
        <div class="col-12 col-lg-8">
            <div class="card jm-card shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="card-title h6 fw-bold mb-0 text-dark">
                        <i class="bi bi-person-gear me-1 text-primary"></i> Profile Information
                    </h5>
                    <small class="text-muted">Update your display name and registered email address</small>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label">Display Name <span class="text-danger">*</span></label>
                            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small">Assigned System Role</label>
                            <div>
                                @php
                                    $profRoleBadge = match($user->role) {
                                        'admin' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                        'engineer' => 'bg-info-subtle text-info border border-info-subtle',
                                        default => 'bg-success-subtle text-success border border-success-subtle',
                                    };
                                @endphp
                                <span class="badge {{ $profRoleBadge }} text-uppercase px-2.5 py-1.5">{{ $user->role }}</span>
                                <small class="text-muted ms-2">(Roles are managed by system administrators)</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 mt-4">
                            <button type="submit" class="btn btn-medical-primary btn-sm">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                            @if (session('status') === 'profile-updated')
                                <span class="text-success small fw-semibold">
                                    <i class="bi bi-check2"></i> Saved successfully.
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Update Password -->
            <div class="card jm-card shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="card-title h6 fw-bold mb-0 text-dark">
                        <i class="bi bi-shield-lock me-1 text-primary"></i> Update Password
                    </h5>
                    <small class="text-muted">Ensure your account is using a long, random password to stay secure</small>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input id="current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input id="password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center gap-3 mt-4">
                            <button type="submit" class="btn btn-medical-primary btn-sm">
                                <i class="bi bi-key me-1"></i> Update Password
                            </button>
                            @if (session('status') === 'password-updated')
                                <span class="text-success small fw-semibold">
                                    <i class="bi bi-check2"></i> Password updated.
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="card jm-card shadow-sm border-danger border-opacity-25">
                <div class="card-header bg-danger bg-opacity-10 py-3 px-4 border-bottom border-danger border-opacity-25">
                    <h5 class="card-title h6 fw-bold mb-0 text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Delete Account
                    </h5>
                    <small class="text-muted">Permanently delete your account credentials</small>
                </div>
                <div class="card-body p-4">
                    <p class="small text-muted mb-3">
                        Once your account is deleted, all associated personal access tokens will be permanently revoked.
                    </p>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
                        <i class="bi bi-trash3 me-1"></i> Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Deletion Confirmation Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fs-6" id="confirmUserDeletionModalLabel">Confirm Account Deletion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">
                            Please enter your account password to confirm you would like to permanently delete your account.
                        </p>
                        <div class="mb-3">
                            <label for="delete_account_password" class="form-label">Account Password</label>
                            <input id="delete_account_password" name="password" type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="Enter password to confirm" required>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger">
                            Permanently Delete Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
