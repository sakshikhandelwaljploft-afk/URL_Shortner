@extends('layouts.app')

@section('content')
<div style="max-width: 580px; margin: 24px auto;">
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Send Invitation</h2>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 2px;">Invite a new member to join the team</p>
            </div>
            <a href="{{ route('invitations.index') }}" class="btn btn-outline" style="padding: 4px 12px; font-size: 0.8rem;">&larr; Back</a>
        </div>

        <form action="{{ route('invitations.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="colleague@company.com" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="role" class="form-label">Assigned Role *</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="">Select a role...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                            {{ $role->value }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            @if(Auth::user()->isSuperAdmin())
                <div class="form-group">
                    <label for="company_id" class="form-label">Organization (SuperAdmin)</label>
                    <select id="company_id" name="company_id" class="form-select">
                        <option value="">Global / System level</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <div class="text-error">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <div style="margin-top: 24px; display: flex; gap: 10px;">
                <button type="submit" class="btn">Send Invite</button>
                <a href="{{ route('invitations.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
