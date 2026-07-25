<x-guest-layout>
    <div class="card border-0 shadow-lg auth-card">
        <div class="card-body p-4 p-lg-5">
            <div class="text-center mb-4">
                <div class="brand-icon mx-auto mb-3"><i class="bi bi-shield-lock"></i></div>
                <h1 class="h4 fw-bold mb-2">Konfirmasi Password</h1>
                <p class="text-secondary mb-0">Masukkan kembali password untuk melanjutkan tindakan sensitif.</p>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password" required autofocus>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Konfirmasi</button>
            </form>
        </div>
    </div>
</x-guest-layout>
