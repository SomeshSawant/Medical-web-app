<x-guest-layout>
    <div class="login-card p-4 p-sm-5">
        <!-- Application Branding -->
        <div class="text-center mb-4">
            <div class="jm-brand-icon mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.75rem;">
                <i class="bi bi-hospital"></i>
            </div>
            <h2 class="h4 fw-bold text-dark mb-1">Jajal Medical</h2>
            <p class="text-muted small mb-0">Medical Case Management System</p>
        </div>

        <!-- Reviewer Test Credentials Quick-Fill Pill -->
        <div class="card bg-light border-0 mb-4 p-3 rounded-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold text-dark">
                    <i class="bi bi-key-fill text-teal me-1 text-primary"></i> Reviewer Demo Accounts:
                </span>
                <span class="badge bg-secondary" style="font-size: 0.65rem;">Click to Autofill</span>
            </div>
            <div class="d-flex flex-column gap-1.5">
                <button type="button" class="btn btn-sm btn-outline-danger text-start py-1 px-2 d-flex justify-content-between align-items-center" onclick="fillCredentials('admin@jajalmedical.test', 'Admin@12345')">
                    <span class="small fw-semibold">Admin (Full Access)</span>
                    <span class="badge bg-danger-subtle text-danger">admin@jajalmedical.test</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-success text-start py-1 px-2 d-flex justify-content-between align-items-center" onclick="fillCredentials('viewer@jajalmedical.test', 'Viewer@12345')">
                    <span class="small fw-semibold">Viewer (Read-Only)</span>
                    <span class="badge bg-success-subtle text-success">viewer@jajalmedical.test</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-info text-start py-1 px-2 d-flex justify-content-between align-items-center" onclick="fillCredentials('engineer@jajalmedical.test', 'Engineer@12345')">
                    <span class="small fw-semibold">Engineer (Bonus Role)</span>
                    <span class="badge bg-info-subtle text-info">engineer@jajalmedical.test</span>
                </button>
            </div>
        </div>

        <!-- Session Status Alert -->
        @if (session('status'))
            <div class="alert alert-info py-2 small mb-3">
                {{ session('status') }}
            </div>
        @endif

        <!-- Invalid Credentials / Error Alert -->
        @if ($errors->has('email') || $errors->has('password'))
            <div class="alert alert-danger py-2 small mb-3 d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                <div>{{ $errors->first('email') ?: $errors->first('password') ?: 'Invalid credentials provided. Please check email and password.' }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <!-- Email Address Input -->
            <div class="mb-3">
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-envelope"></i></span>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email', 'admin@jajalmedical.test') }}" 
                           required 
                           autofocus 
                           autocomplete="username" 
                           class="form-control @error('email') is-invalid @enderror" 
                           placeholder="name@jajalmedical.test">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Password Input with Show/Hide Toggle -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label mb-0">Password <span class="text-danger">*</span></label>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-lock"></i></span>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           value="Admin@12345" 
                           required 
                           autocomplete="current-password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Enter your password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" title="Toggle password visibility">
                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Remember Me Checkbox -->
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label small text-muted" for="remember">
                        Remember me on this browser
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-jajal-primary py-2.5 fw-semibold shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Clinical System
                </button>
            </div>
        </form>

        <div class="text-center mt-3 pt-3 border-top">
            <span class="text-muted" style="font-size: 0.72rem;">
                Authorized medical personnel only &bull; Access is monitored and audited.
            </span>
        </div>
    </div>

    <!-- Script for Show/Hide Password and Quick Fill -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passwordInput && toggleIcon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    if (isPassword) {
                        passwordInput.setAttribute('type', 'text');
                        toggleIcon.classList.remove('bi-eye');
                        toggleIcon.classList.add('bi-eye-slash');
                    } else {
                        passwordInput.setAttribute('type', 'password');
                        toggleIcon.classList.remove('bi-eye-slash');
                        toggleIcon.classList.add('bi-eye');
                    }
                });
            }
        });

        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</x-guest-layout>
