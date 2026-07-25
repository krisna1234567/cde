<x-guest-layout>
    <div class="card border-0 shadow-lg auth-card">
        <div class="card-body p-4 p-lg-5">
            <div class="text-center mb-4">
                <div class="brand-icon mx-auto mb-3"><i class="bi bi-building-gear"></i></div>
                <h1 class="h4 fw-bold mb-1">Admin CMS</h1>
                <p class="text-secondary mb-0">Masuk untuk mengelola konten website.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success" role="alert">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="username" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="small text-decoration-none">Lupa password?</a>
                        @endif
                    </div>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Masuk</button>
            </form>
        </div>
    </div>
</x-guest-layout>
