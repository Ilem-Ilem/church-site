<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.home-auth')] class extends Component {

    public string $email = '';
    public string $password = '';
    public string $chapter = '';
    public bool $remember = false;

    public array $chapters = [];

    public function mount(): void
    {
        // Load all chapter names
        $this->chapters = \App\Models\Chapter::orderBy('name')
            ->pluck('name', 'name')
            ->toArray();
    }

    public function login(): void
    {
        // 1. Validate inputs
        $this->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string|min:6',
            'chapter'  => 'required|string|exists:chapters,name',
        ]);

        // 2. Rate limiting
        $this->ensureIsNotRateLimited();

        // 3. Attempt authentication
        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // 4. Load user with chapter relationship
        $user = Auth::user()->loadMissing('chapter');

        // 5. Validate user belongs to selected chapter
        if (! $user->chapter || $user->chapter->name !== $this->chapter) {
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();

            throw ValidationException::withMessages([
                'chapter' => 'You do not have access to this chapter.',
            ]);
        }

        // 6. Success: clear rate limiter
        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // 7. Redirect with chapter param
        $redirectUrl = route('home', absolute: false);
        if ($this->chapter) {
            $redirectUrl .= '?chapter=' . urlencode($this->chapter);
        }

        $this->redirect($redirectUrl);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
};
?>

<style>
    :root {
        --primary: #357be4;
        --primary-dark: #294dc0;
        --danger: #dc3545;
        --success: #28a745;
        --gray: #6c757d;
        --light: #f8f9fa;
        --dark: #343a40;
    }

    .auth-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .auth-container {
        max-width: 450px;
        width: 100%;
    }

    .auth-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        padding: 2.5rem;
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .auth-header {
        text-align: center;
        margin-bottom: 1.8rem;
    }

    .auth-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .auth-header p {
        font-size: 0.9rem;
        color: #666;
    }

    .form-label {
        font-weight: 500;
        color: #333;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-control {
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(53, 123, 228, 0.15);
    }

    .form-control.is-invalid {
        border-color: var(--danger);
    }

    .invalid-feedback {
        font-size: 0.85rem;
        color: var(--danger);
        margin-top: 0.25rem;
    }

    .form-check {
        margin: 1rem 0;
    }

    .form-check-input {
        margin-right: 0.5rem;
    }

    .form-check-label {
        font-size: 0.9rem;
        color: #555;
        cursor: pointer;
    }

    .auth-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 1.2rem 0;
        font-size: 0.9rem;
    }

    .forgot-password {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }

    .forgot-password:hover {
        text-decoration: underline;
    }

    .btn-login {
        width: 100%;
        padding: 0.8rem;
        background: linear-gradient(to right, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(53, 123, 228, 0.3);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .btn-login:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .alert {
        border-radius: 0.5rem;
        font-size: 0.9rem;
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 600px) {
        .auth-card {
            padding: 2rem 1.5rem;
        }
        .auth-header h1 {
            font-size: 1.5rem;
        }
    }
</style>

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-card">

            <!-- Header -->
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your account to continue</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Login Form -->
            <form wire:submit="login">
                {{-- @csrf --}}

                <!-- Chapter Select -->
                <div class="mb-3">
                    <label for="chapter" class="form-label">Select Chapter</label>
                    <select
                        id="chapter"
                        class="form-control @error('chapter') is-invalid @enderror"
                        wire:model.live="chapter"
                        required
                        autofocus
                    >
                        <option value="">-- Select a Chapter --</option>
                        @foreach ($chapters as $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    {{$chapter}}
                    @error('chapter')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        wire:model.live="email"
                        placeholder="your.email@example.com"
                        autocomplete="email"
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        wire:model.live="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember & Forgot -->
                <div class="auth-footer">
                    <div class="form-check">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="remember"
                            wire:model="remember"
                        >
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password" wire:navigate>
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="btn-login"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove.delay>Sign In</span>
                    <span wire:loading.delay>Signing in...</span>
                </button>
            </form>

        </div>
    </div>
</div>
