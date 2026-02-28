<x-layout.public>
    <div class="row">
        <h1 class="h3 mb-3 fw-normal text-center">Login</h1>

        <div>
            <form method="POST" action="/login">
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

                <div class="mb-3">
                    <label for="fid-password" class="form-label">Password</label>
                    <input type="password" name="password" id="fid-password"
                           class="form-control @error('password') is-invalid @enderror"
                           value="{{ old('password', '') }}">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" name="remember" value="yes" id="fid-rememberMe">
                  <label class="form-check-label" for="fid-rememberMe">
                    Remember Me
                  </label>
                </div>



                <div class="d-grid gap-2 col-12 mx-auto">
                    <button type="submit" class="btn btn-primary mb-3">Login</button>
                </div>

            </form>

            <p class="text-muted text-center">
{{--                <a href="/recover">Recover</a> | --}}
                <a href="/register">Register</a>
            </p>
        </div>
    </div>
</x-layout.public>
