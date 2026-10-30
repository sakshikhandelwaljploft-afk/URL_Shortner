@extends('layouts.app')

@section('content')
<div style="max-width: 420px; margin: 48px auto;">
    <div class="card">
        <div class="card-header" style="justify-content: center; text-align: center; margin-bottom: 24px;">
            <div>
                <h2 class="card-title" style="font-size: 1.4rem;">Sign In</h2>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">
                    Enter your workspace credentials to continue
                </p>
            </div>
        </div>

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="you@company.com" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                @error('password')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; cursor: pointer; margin-bottom: 0;">
                    <input type="checkbox" id="remember" name="remember">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn" style="width: 100%; margin-top: 12px; padding: 10px;">Sign In</button>
        </form>

        <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border); font-size: 0.75rem; color: var(--text-muted); text-align: center;">
            Seed accounts use password: <code>password123</code>
        </div>
    </div>
</div>
@endsection
