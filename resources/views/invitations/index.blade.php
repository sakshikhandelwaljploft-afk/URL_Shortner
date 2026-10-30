@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">User Invitations</h2>
            <p style="color: var(--text-muted); font-size: 0.875rem;">
                Invite and onboard colleagues into your organization.
            </p>
        </div>
        <div>
            <a href="{{ route('invitations.create') }}" class="btn">+ Invite User</a>
        </div>
    </div>

    @if($invitations->isEmpty())
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            No active invitations sent yet. Click "+ Invite User" above to send one.
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Company</th>
                    <th>Sent By</th>
                    <th>Status</th>
                    <th>Invite Link</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invitations as $invitation)
                    <tr>
                        <td><strong>{{ $invitation->email }}</strong></td>
                        <td>
                            <span class="badge">{{ $invitation->role->value }}</span>
                        </td>
                        <td>{{ $invitation->company->name ?? 'System' }}</td>
                        <td>{{ $invitation->inviter->name ?? 'System' }}</td>
                        <td>
                            <span class="badge" style="background-color: #fef3c7; color: #92400e;">
                                {{ ucfirst($invitation->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('invitations.accept', ['token' => $invitation->token]) }}" target="_blank" style="color: var(--primary); font-size: 0.8rem; text-decoration: underline;">
                                Copy Link &nearr;
                            </a>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.8rem;">
                            {{ $invitation->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $invitations->links() }}
        </div>
    @endif
</div>
@endsection
