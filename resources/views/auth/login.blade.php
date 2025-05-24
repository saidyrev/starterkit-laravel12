@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<!-- Logo -->
<div class="auth-logo">
    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="32" height="32" rx="8" fill="white"/>
        <path d="M12 8H20C21.1046 8 22 8.89543 22 10V14C22 15.1046 21.1046 16 20 16H16V20C16 21.1046 15.1046 22 14 22H10C8.89543 22 8 21.1046 8 20V12C8 9.79086 9.79086 8 12 8Z" fill="#696cff"/>
        <circle cx="18" cy="18" r="3" fill="#696cff" fill-opacity="0.8"/>
    </svg>
</div>

<!-- Title -->
<h2 class="auth-title">Welcome Back! 👋</h2>
<p class="auth-subtitle">Sign in to your account to continue</p>

<!-- Session Status -->
@if (session('status'))
<div class="success-alert">
    <i class="bx bx-check-circle me-2"></i>
    {{ session('status') }}
</div>
@endif

<!-- Validation Errors -->
@if ($errors->any())
<div class="error-alert">
    <i class="bx bx-error-circle me-2"></i>
    <strong>Whoops!</strong> There were some problems with your input.
    <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Login Form -->
<form method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf

    <!-- Email -->
    <div class="form-floating">
        <input type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               id="email" 
               name="email" 
               value="{{ old('email') }}" 
               placeholder="Enter your email"
               required 
               autofocus 
               autocomplete="username">
        <label for="email">Email Address</label>
        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Password -->
    <div class="form-floating">
        <input type="password" 
               class="form-control @error('password') is-invalid @enderror" 
               id="password" 
               name="password" 
               placeholder="Enter your password"
               required 
               autocomplete="current-password">
        <label for="password">Password</label>
        <div class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;" onclick="togglePassword()">
            <i class="bx bx-show" id="passwordToggle"></i>
        </div>
        @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Remember Me & Forgot Password -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
            <label class="form-check-label" for="remember_me">
                Remember me
            </label>
        </div>
        @if (Route::has('password.request'))
            <a class="auth-link" href="{{ route('password.request') }}">
                Forgot password?
            </a>
        @endif
    </div>

    <!-- Login Button -->
    <button type="submit" class="btn btn-primary w-100 mb-3" id="loginBtn">
        <i class="bx bx-log-in-circle me-2"></i>
        Sign In
    </button>
</form>

<!-- Divider -->
{{-- <div class="divider">
    <span>Or continue with</span>
</div> --}}

<!-- Social Login (Optional) -->
{{-- <div class="row g-2 mb-4">
    <div class="col-6">
        <a href="#" class="social-login">
            <i class="bx bxl-google"></i>
            Google
        </a>
    </div>
    <div class="col-6">
        <a href="#" class="social-login">
            <i class="bx bxl-github"></i>
            GitHub
        </a>
    </div>
</div> --}}

<!-- Register Link -->
@if (Route::has('register'))
{{-- <div class="text-center">
    <span class="text-muted">Don't have an account? </span>
    <a href="{{ route('register') }}" class="auth-link">
        Create one here
    </a>
</div> --}}
@endif

@push('scripts')
<script>
$(document).ready(function() {
    // Form submit with loading state
    $('#loginForm').submit(function() {
        $('#loginBtn').addClass('btn-loading').prop('disabled', true);
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.success-alert, .error-alert').fadeOut('slow');
    }, 5000);

    // Add some entrance animations
    $('.auth-card').css({
        'opacity': '0',
        'transform': 'translateY(30px)'
    }).animate({
        'opacity': '1',
        'transform': 'translateY(0px)'
    }, 800);
});

// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bx-show');
        toggleIcon.classList.add('bx-hide');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bx-hide');
        toggleIcon.classList.add('bx-show');
    }
}

// Add floating effect to shapes
document.addEventListener('DOMContentLoaded', function() {
    const shapes = document.querySelectorAll('.floating-shapes .shape');
    shapes.forEach((shape, index) => {
        const delay = index * 2000;
        shape.style.animationDelay = delay + 'ms';
    });
});
</script>

    <script>
        // Notifikasi untuk login gagal
        @if(session('login_error'))
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal!',
                text: '{{ session('login_error') }}',
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Coba Lagi'
            });
        @endif

        // Jika ada validation errors
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal!',
                text: 'Periksa kembali email dan password Anda.',
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Coba Lagi'
            });
        @endif
    </script>
@endpush
@endsection