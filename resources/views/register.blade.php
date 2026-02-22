<x-layout.public>
    <div class="row">
        <h1 class="h3 mb-3 fw-normal text-center">Account Registration</h1>

        <div>
            <form method="POST" action="/register">
                @csrf

                <div class="mb-3">
                    <label for="fid-email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="fid-email"  placeholder="name@example.com"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', '') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fid-name" class="form-label">Name</label>
                    <input type="text" name="name" id="fid-name" placeholder="Your Name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', '') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fid-password" class="form-label">Password</label>
                    <input type="password" name="password" id="fid-password"
                           class="form-control @error('password') is-invalid @enderror"
                           value="{{ old('password', '') }}">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 col-12 mx-auto">
                    <button type="submit" class="btn btn-primary mb-3" data-test="btn-register">Register</button>
                </div>

            </form>

            <p class="text-muted text-center">
{{--                <a href="/recover">Recover</a> | --}}
                <a href="/login">Login</a>
            </p>
        </div>
    </div>
</x-layout.public>
