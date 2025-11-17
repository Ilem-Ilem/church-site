<?php

use App\Models\User;
use App\Models\PasswordResetRequest;
use App\Notifications\PasswordResetRequestNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new #[Layout('components.layouts.home-auth')] class extends Component {
    #[Validate('required|string|email|exists:users,email')]
    public string $email = '';

    public bool $submitted = false;

    /**
     * Send a password reset request for approval
     */
    public function sendPasswordReset(): void
    {
        $validated = $this->validate([
            'email' => 'required|string|email|exists:users,email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            // Check if there's already a pending request
            $existingRequest = PasswordResetRequest::where('email', $validated['email'])
                ->where('status', 'pending')
                ->first();

            if ($existingRequest) {
                $this->addError('email', 'You already have a pending password reset request. Please wait for it to be approved.');
                return;
            }

            // Create a password reset request
            $resetRequest = PasswordResetRequest::create([
                'user_id' => $user->id,
                'email' => $validated['email'],
                'token' => Str::random(60),
                'status' => 'pending',
            ]);

            // Send notification to the user
            $user->notify(new PasswordResetRequestNotification($resetRequest));

            $this->submitted = true;
            $this->reset('email');
        }
    }
}; ?>

<style>
    body {
        background: linear-gradient(135deg, #357be4, #294dc0);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .auth-container {
        max-width: 450px;
        width: 100%;
        margin: 0 auto;
        padding: 20px;
    }

    .auth-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        padding: 40px;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .auth-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }

    .auth-header p {
        font-size: 14px;
        color: #666;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #333;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: #357be4;
        box-shadow: 0 0 0 3px rgba(53, 123, 228, 0.1);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    .btn-submit {
        width: 100%;
        padding: 12px;
        background: linear-gradient(to right, #357be4, #294dc0);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        margin-top: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(53, 123, 228, 0.4);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .alert {
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-success {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #842029;
        border: 1px solid #f5c6cb;
    }

    .alert-info {
        background-color: #cfe2ff;
        color: #084298;
        border: 1px solid #b6d4fe;
    }

    .success-icon {
        font-size: 48px;
        color: #0f5132;
        text-align: center;
        margin-bottom: 20px;
    }

    .success-content {
        text-align: center;
    }

    .success-content h2 {
        font-size: 24px;
        color: #333;
        margin-bottom: 15px;
    }

    .success-content p {
        color: #666;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .back-link {
        text-align: center;
        margin-top: 20px;
    }

    .back-link a {
        color: #357be4;
        text-decoration: none;
        font-weight: 600;
    }

    .back-link a:hover {
        text-decoration: underline;
    }

    .info-box {
        background-color: #f0f7ff;
        border: 1px solid #b6d4fe;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #084298;
    }

    @media (max-width: 600px) {
        .auth-card {
            padding: 30px 20px;
        }

        .auth-header h1 {
            font-size: 24px;
        }
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        @if ($submitted)
            <div class="success-content">
                <div class="success-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h2>Check Your Email</h2>
                <p>
                    We've sent a password recovery request to your email. Our team will review your request and 
                    send you a password reset link once it's approved.
                </p>
                <p style="color: #999;">
                    This usually takes a few hours. Please check your email and spam folder.
                </p>
                <div class="back-link" style="margin-top: 30px;">
                    <a href="{{ route('home.login') }}" wire:navigate>Back to Login</a>
                </div>
            </div>
        @else
            <div class="auth-header">
                <h1>Reset Password</h1>
                <p>Enter your email address and we'll send you a password recovery request</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">Please fix the following errors:</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="info-box">
                <i class="bi bi-info-circle me-2"></i>
                Your request will be reviewed and approved by our team before you can reset your password.
            </div>

            <form wire:submit="sendPasswordReset">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        id="email"
                        placeholder="your.email@example.com" 
                        wire:model="email"
                        autofocus
                        autocomplete="email"
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">
                    <span wire:loading.remove>Send Recovery Request</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...
                    </span>
                </button>
            </form>

            <div class="back-link">
                <a href="{{ route('home.login') }}" wire:navigate>Back to Login</a>
            </div>
        @endif
    </div>
</div>
