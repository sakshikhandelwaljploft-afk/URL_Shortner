@extends('layouts.app')

@section('content')
<div style="max-width: 580px; margin: 24px auto;">
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Create Short URL</h2>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 2px;">Generate a shortened redirect link</p>
            </div>
            <a href="{{ route('urls.index') }}" class="btn btn-outline" style="padding: 4px 12px; font-size: 0.8rem;">&larr; Back</a>
        </div>

        <form action="{{ route('urls.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="original_url" class="form-label">Destination URL *</label>
                <input type="url" id="original_url" name="original_url" class="form-input" placeholder="https://example.com/very/long/path/or-campaign" value="{{ old('original_url') }}" required autofocus>
                @error('original_url')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="code" class="form-label">Custom Slug (Optional)</label>
                <input type="text" id="code" name="code" class="form-input" placeholder="e.g. promo-2026 (leave blank for random)" value="{{ old('code') }}">
                <small style="color: var(--text-muted); font-size: 0.75rem;">Alphanumeric characters only. Auto-generated if left blank.</small>
                @error('code')
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: 24px; display: flex; gap: 10px;">
                <button type="submit" class="btn">Create Short Link</button>
                <a href="{{ route('urls.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
