@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Short URLs</h2>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 2px;">
                Manage your active shortened links and track redirect performance.
            </p>
        </div>
        <div>
            @if($canCreate)
                <a href="{{ route('urls.create') }}" class="btn">+ New Short URL</a>
            @endif
        </div>
    </div>

    @if($urls->isEmpty())
        <div style="text-align: center; padding: 48px 16px; color: var(--text-muted);">
            <p style="font-size: 1rem; margin-bottom: 8px;">No short URLs found.</p>
            @if($canCreate)
                <a href="{{ route('urls.create') }}" class="btn btn-outline" style="font-size: 0.8rem;">Create your first link</a>
            @endif
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Short Code</th>
                    <th>Resolved Link</th>
                    <th>Target Destination</th>
                    <th>Company</th>
                    <th>Created By</th>
                    <th>Clicks</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($urls as $url)
                    <tr>
                        <td>
                            <code>{{ $url->code }}</code>
                        </td>
                        <td>
                            <a href="{{ route('urls.resolve', ['code' => $url->code]) }}" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                                /{{ $url->code }} &nearr;
                            </a>
                        </td>
                        <td style="max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <a href="{{ $url->original_url }}" target="_blank" style="color: var(--text-muted);" title="{{ $url->original_url }}">
                                {{ $url->original_url }}
                            </a>
                        </td>
                        <td>
                            <span class="badge" style="background-color: #f1f5f9; color: #334155;">
                                {{ $url->company->name ?? 'System' }}
                            </span>
                        </td>
                        <td>
                            {{ $url->user->name ?? 'System' }}
                            <small style="color: var(--text-muted);">({{ $url->user->role->value ?? '' }})</small>
                        </td>
                        <td>
                            <strong>{{ $url->clicks }}</strong>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.8rem;">
                            {{ $url->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $urls->links() }}
        </div>
    @endif
</div>
@endsection
