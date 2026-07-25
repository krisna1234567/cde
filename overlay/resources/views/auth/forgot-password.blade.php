<x-guest-layout>
    <div class="card border-0 shadow-lg auth-card">
        <div class="card-body p-4 p-lg-5">
            <div class="text-center mb-4">
                <div class="brand-icon mx-auto mb-3"><i class="bi bi-envelope-lock"></i></div>
                <h1 class="h4 fw-bold mb-2">Lupa Password</h1>
                <p class="text-secondary mb-0">Masukkan email admin untuk menerima tautan reset password.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success" role="alert">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="email" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Kirim Tautan Reset</button>
                <a href="{{ route('login') }}" class="btn btn-link w-100 mt-2 text-decoration-none">Kembali ke login</a>
            </form>
        </div>
    </div>
</x-guest-layout>
