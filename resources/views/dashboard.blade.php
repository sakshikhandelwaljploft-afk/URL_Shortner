<?php
/**
 * Main dashboard view
 */
?>
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h1 class="card-title">Welcome back, {{ $user->name }}</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">
                Logged in as <strong>{{ $user->role->value }}</strong>
                @if($user->company)
                    &bull; {{ $user->company->name }}
                @else
                    &bull; System Administration
                @endif
            </p>
        </div>
        <div>
            <span class="badge" style="font-size: 0.8rem; padding: 4px 8px; background: #f1f5f9; color: #475569;">
                Status: Active
            </span>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin: 20px 0;">
        <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 6px; padding: 16px;">
            <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Companies</div>
            <div style="font-size: 1.75rem; font-weight: 700; margin-top: 4px;">{{ $stats['totalCompanies'] }}</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 6px; padding: 16px;">
            <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Total Users</div>
            <div style="font-size: 1.75rem; font-weight: 700; margin-top: 4px;">{{ $stats['totalUsers'] }}</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 6px; padding: 16px;">
            <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Short Links</div>
            <div style="font-size: 1.75rem; font-weight: 700; margin-top: 4px;">{{ $stats['totalUrls'] }}</div>
        </div>
    </div>

    <!-- Actions Bar -->
    <div style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        @can('viewAny', App\Models\ShortUrl::class)
            <a href="{{ route('urls.index') }}" class="btn">View Short Links</a>
        @endcan

        @can('create', App\Models\ShortUrl::class)
            <a href="{{ route('urls.create') }}" class="btn btn-outline">+ Create Link</a>
        @endcan

        <a href="{{ route('invitations.index') }}" class="btn btn-outline">Team Invitations</a>
    </div>

    <!-- Workspace Info -->
    <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 6px; padding: 14px 18px; margin-top: 24px;">
        <div style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Workspace Info</div>
        <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.6; margin: 0;">
            Short links are scoped based on your assigned role permissions. Invitations can be sent to collaborate with other team members across organizations.
        </p>
    </div>
</div>
@endsection
