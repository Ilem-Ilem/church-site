<?php

use App\Models\{PasswordResetRequest, User};
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use TallStackUi\Traits\Interactions;

new #[Layout('components.layouts.home-auth')] class extends Component {
    use Interactions;

    public $token;
    public $email;
    public $password;
    public $password_confirmation;
    public $resetRequest;

    public function mount($token)
    {
        $this->token = $token;

        // Find the reset request
        $this->resetRequest = PasswordResetRequest::where('token', $token)
            ->where('status', 'approved')
            ->first();

        if (!$this->resetRequest) {
            abort(404, 'Invalid or expired reset link');
        }

        $this->email = $this->resetRequest->email;
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Update user password
        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', 'User not found');
            return;
        }

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        // Mark request as used
        $this->resetRequest->update([
            'status' => 'used',
            'used_at' => now(),
        ]);

        session()->flash('status', 'Password has been reset successfully! You can now login with your new password.');
        return redirect()->route('home.login');
    }
}; ?>

<style>
    .auth-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, #357be4, #294dc0);
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
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
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
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        transition: all 0.2s;
        margin-bottom: 1rem;
    }

    .form-control:focus {
        outline: none;
        border-color: #357be4;
        box-shadow: 0 0 0 0.2rem rgba(53, 123, 228, 0.15);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        font-size: 0.85rem;
        color: #dc3545;
        margin-top: -0.75rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .btn-submit {
        width: 100%;
        padding: 0.8rem;
        background: linear-gradient(to right, #357be4, #294dc0);
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(53, 123, 228, 0.3);
    }

    .btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .password-hint {
        font-size: 0.8rem;
        color: #666;
        margin-top: -0.5rem;
        margin-bottom: 1rem;
    }
</style>

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Reset Your Password</h1>
                <p>Enter your new password below</p>
            </div>

            <form wire:submit="resetPassword">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        class="form-control"
                        value="{{ $email }}"
                        readonly
                        disabled
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input
                        type="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        wire:model="password"
                        placeholder="Enter new password"
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <p class="password-hint">Minimum 8 characters</p>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        class="form-control @error('password_confirmation') is-invalid @enderror"
                        wire:model="password_confirmation"
                        placeholder="Confirm new password"
                        required
                    >
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Reset Password</span>
                    <span wire:loading>Resetting...</span>
                </button>
            </form>
        </div>
    </div>
</div>
