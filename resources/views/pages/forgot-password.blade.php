<x-pages.public>
    <div class="row">
        <h1 class="h3 mb-3 fw-normal text-center">Forgot Password</h1>

        <div>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <label for="fid-email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="fid-email" placeholder="name@example.com"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', '') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 col-12 mx-auto">
                    <button type="submit" class="btn btn-primary mb-3">Send Reset Link</button>
                </div>
            </form>

            <p class="text-muted text-center">
                <a href="/login">Login</a>
            </p>
        </div>
    </div>
</x-pages.public>
