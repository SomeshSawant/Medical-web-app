<x-guest-layout>
    <!-- Header -->
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-gray-900">Medical Case Management</h1>
        <p class="text-xs text-gray-500 mt-1">Implant Tracking &amp; Surgical Scheduling System</p>
    </div>

    <!-- Reviewer Credentials Quick-Reference Box -->
    <div class="mb-5 p-3.5 bg-indigo-50/70 border border-indigo-200 rounded-lg text-xs text-gray-700">
        <p class="font-semibold text-indigo-900 mb-1 flex items-center">
            <svg class="w-4 h-4 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Reviewer Test Credentials:
        </p>
        <div class="space-y-1 font-mono text-[11px] text-gray-800">
            <div><span class="font-semibold text-purple-700">Admin:</span> admin@example.com / password123</div>
            <div><span class="font-semibold text-emerald-700">Viewer:</span> viewer@example.com / password123</div>
            <div><span class="font-semibold text-blue-700">Engineer:</span> engineer@example.com / password123</div>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error Alert on Failed Login -->
    @if ($errors->has('email') || $errors->has('password'))
        <div class="mb-4 rounded-md bg-rose-50 border border-rose-200 p-3 text-xs text-rose-800 flex items-center">
            <svg class="h-4 w-4 text-rose-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $errors->first('email') ?: $errors->first('password') ?: 'Invalid email or password.' }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full text-sm" type="email" name="email" :value="old('email', 'admin@example.com')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full text-sm"
                            type="password"
                            name="password"
                            value="password123"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember" checked>
                <span class="ms-2 text-xs text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-5">
            <x-primary-button class="w-full justify-center py-2.5 bg-indigo-600 hover:bg-indigo-700">
                {{ __('Sign In to Portal') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
