<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();
        if (! $user || ! $user->hasRole('super-admin')) {
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Only super admins can sign in here.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('admin.super-admin.dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
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

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="min-h-screen bg-[#f4f1ea]">
    <div class="relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute -left-40 top-[-10rem] h-[28rem] w-[28rem] rounded-full bg-gradient-to-br from-indigo-200 via-indigo-100 to-transparent opacity-60 blur-3xl"></div>
            <div class="absolute right-[-12rem] top-[8rem] h-[26rem] w-[26rem] rounded-full bg-gradient-to-tr from-slate-200 via-slate-100 to-transparent opacity-60 blur-3xl"></div>
        </div>
        <div class="relative mx-auto flex min-h-screen w-full max-w-5xl items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-2xl rounded-3xl border border-slate-200/70 bg-white/85 p-8 shadow-[0_25px_60px_-35px_rgba(15,23,42,0.55)] backdrop-blur sm:p-10">
                <div class="mb-8 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 shadow-inner">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3v18M6 9h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Super Admin</p>
                    <h1 class="mt-2 text-3xl font-semibold text-slate-900">Secure Access Portal</h1>
                    <p class="mt-2 text-sm text-slate-600">Sign in to manage the entire church system.</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" wire:submit="login" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email Address</label>
                        <input
                            id="email"
                            type="email"
                            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100 @error('email') border-rose-300 focus:border-rose-400 focus:ring-rose-100 @enderror"
                            wire:model="email"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="admin@example.com"
                        />
                        @error('email')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                        <input
                            id="password"
                            type="password"
                            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-100 @error('password') border-rose-300 focus:border-rose-400 focus:ring-rose-100 @enderror"
                            wire:model="password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter your password"
                        />
                        @error('password')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4 text-sm text-slate-600">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-700 focus:ring-slate-200" wire:model="remember" />
                            <span>Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="font-medium text-slate-700 transition hover:text-slate-900" wire:navigate>
                                Forgot Password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/20 transition hover:-translate-y-0.5 hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200" wire:loading.attr="disabled">
                        <span wire:loading.remove.delay>Sign In</span>
                        <span wire:loading.delay>Signing in...</span>
                    </button>
                </form>

                <div class="mt-8 border-t border-slate-200/80 pt-6 text-center text-xs text-slate-500">
                    Restricted access. Use your super admin credentials.
                </div>
            </div>
        </div>
    </div>
</div>
