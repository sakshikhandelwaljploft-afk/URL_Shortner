@extends('layouts.app')

@section('content')
<div style="max-width: 500px; margin: 40px auto;">
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Accept Invitation</h2>
                <p style="color: var(--text-muted); font-size: 0.85rem;">
                    You have been invited to join
                    <strong>{{ $invitation->company->name ?? 'the organization' }}</strong>
                    as a <strong>{{ $invitation->role->value }}</strong>.
                </p>
            </div>
        </div>

        <form action="{{ route('invitations.processAccept', ['token' => $invitation->token]) }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-input" value="{{ $invitation->email }}" disabled style="background-color: #f1f5f9; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Full Name *</label>
                <input type="text" id="name" name="name" class="form-input" placeholder="e.g. John Doe" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Choose Password *</label>
                <input type="password" id="password" name="password" class="form-input" required minlength="8">
                @error('password')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required minlength="8">
            </div>

            <button type="submit" class="btn" style="width: 100%; margin-top: 12px;">Activate Account & Login</button>
        </form>
    </div>
</div>
@endsection

