<x-guest-layout>
    <div class="card border-0 shadow-lg auth-card">
        <div class="card-body p-4 p-lg-5">
            <div class="text-center mb-4">
                <div class="brand-icon mx-auto mb-3"><i class="bi bi-key"></i></div>
                <h1 class="h4 fw-bold mb-1">Reset Password</h1>
                <p class="text-secondary mb-0">Buat password baru untuk akun admin.</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', request('email')) }}" class="form-control @error('email') is-invalid @enderror" autocomplete="email" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Simpan Password Baru</button>
            </form>
        </div>
    </div>
</x-guest-layout>
