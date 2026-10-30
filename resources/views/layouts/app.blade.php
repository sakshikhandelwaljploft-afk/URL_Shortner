<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'URL Shortener Service' }}</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.5;
            padding-bottom: 40px;
        }
        .navbar {
            background-color: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .nav-links a {
            color: var(--text);
            text-decoration: none;
            font-weight: 500;
        }
        .nav-links a:hover {
            color: var(--primary);
        }
        .user-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
            background-color: #e0e7ff;
            color: #3730a3;
        }
        .badge-admin { background-color: #fef3c7; color: #92400e; }
        .badge-super { background-color: #fce7f3; color: #9d174d; }
        .badge-sales { background-color: #d1fae5; color: #065f46; }
        .badge-manager { background-color: #e0f2fe; color: #075985; }
        .container {
            max-width: 1000px;
            margin: 32px auto;
            padding: 0 16px;
        }
        .card {
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .card-header {
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            background-color: var(--primary);
            color: #ffffff;
            padding: 8px 16px;
            font-size: 0.875rem;
            font-weight: 500;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
        }
        .btn:hover { background-color: var(--primary-hover); }
        .btn-outline {
            background-color: transparent;
            color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-outline:hover { background-color: var(--border); }
        .btn-danger { background-color: var(--danger); }
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.875rem;
        }
        .alert-success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-info { background-color: #eff6ff; color: #1e40af; border: 1px solid #dbeafe; }
        .alert-warning { background-color: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            margin-top: 12px;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        .table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: var(--text-muted);
        }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-weight: 500; margin-bottom: 6px; font-size: 0.875rem; }
        .form-input, .form-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.875rem;
        }
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary);
        }
        .text-error { color: var(--danger); font-size: 0.75rem; margin-top: 4px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 640px) {
            .grid-2 { grid-template-columns: 1fr; }
            .navbar { flex-direction: column; gap: 12px; }
        }
    </style>
</head>
<body>
    @auth
    <nav class="navbar">
        <div style="display: flex; align-items: center; gap: 24px;">
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                🔗 URL Shortener
            </a>
            <div class="nav-links">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                @can('viewAny', App\Models\ShortUrl::class)
                    <a href="{{ route('urls.index') }}">Short URLs</a>
                @endcan
                <a href="{{ route('invitations.index') }}">Invitations</a>
            </div>
        </div>
        <div class="user-section">
            <span style="font-size: 0.875rem;">
                <strong>{{ Auth::user()->name }}</strong>
                @if(Auth::user()->company)
                    <span style="color: var(--text-muted);">({{ Auth::user()->company->name }})</span>
                @endif
            </span>
            @php
                $roleClass = match(Auth::user()->role->value) {
                    'SuperAdmin' => 'badge-super',
                    'Admin' => 'badge-admin',
                    'Sales' => 'badge-sales',
                    'Manager' => 'badge-manager',
                    default => 'badge',
                };
            @endphp
            <span class="badge {{ $roleClass }}">{{ Auth::user()->role->value }}</span>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-outline" style="padding: 4px 10px; font-size: 0.8rem;">Log out</button>
            </form>
        </div>
    </nav>
    @endauth

    <div class="container">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>

