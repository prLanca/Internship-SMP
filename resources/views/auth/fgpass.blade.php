

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<div class="container mt-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white text-center">
                    <h4>{{ __('Reset Password') }}</h4>
                </div>
                <div class="card-body">

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <!-- Email Input -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email address') }}</label>
                            <input type="email" name="email" id="email" class="form-control rounded-pill @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus placeholder="{{ __('Enter email') }}">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Button -->
                        <div class="text-center position-relative">
                            <button type="submit" class="btn btn-danger btn-block rounded-pill">{{ __('Send Password Reset Link') }}</button>
                        </div>

                    </form>
                </div>
                <div class="card-footer bg-light">
                    <div class="text-center">
                        {{ __('Remembered your password?') }} <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
