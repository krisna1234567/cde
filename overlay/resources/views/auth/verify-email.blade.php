<x-guest-layout>
    <div class="card border-0 shadow-lg auth-card">
        <div class="card-body p-4 p-lg-5">
            <div class="text-center mb-4">
                <div class="brand-icon mx-auto mb-3"><i class="bi bi-patch-check"></i></div>
                <h1 class="h4 fw-bold mb-2">Verifikasi Email</h1>
                <p class="text-secondary mb-0">Periksa email Anda dan klik tautan verifikasi yang dikirimkan sistem.</p>
            </div>

            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success">Tautan verifikasi baru telah dikirim.</div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mb-2">
                @csrf
                <button type="submit" class="btn btn-primary w-100">Kirim Ulang Tautan</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link w-100 text-decoration-none">Logout</button>
            </form>
        </div>
    </div>
</x-guest-layout>
