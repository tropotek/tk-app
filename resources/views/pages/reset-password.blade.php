<x-pages.public>
    <div class="row">
        <h1 class="h3 mb-3 fw-normal text-center">Reset Password</h1>

        <div>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="fid-email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="fid-email" placeholder="name@example.com"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fid-password" class="form-label">New Password</label>
                    <input type="password" name="password" id="fid-password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fid-password-confirm" class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="fid-password-confirm"
                           class="form-control">
                </div>

                <div class="d-grid gap-2 col-12 mx-auto">
                    <button type="submit" class="btn btn-primary mb-3">Reset Password</button>
                </div>
            </form>

            <p class="text-muted text-center">
                <a href="/login">Login</a>
            </p>
        </div>
    </div>
</x-pages.public>
