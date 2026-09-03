<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'CEM Morondava'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --cem-ink: #17343b;
            --cem-green: #1c7c6c;
            --cem-sand: #f4efe7;
            --cem-accent: #d87c4d;
        }
        body {
            background: radial-gradient(circle at top, #f5fbfa 0, #eef5f2 40%, #e8efe9 100%);
            color: var(--cem-ink);
        }
        .cem-navbar {
            background: linear-gradient(135deg, #10363a 0%, #1b6b61 100%);
            box-shadow: 0 12px 30px rgba(16, 54, 58, 0.2);
        }
        .cem-navbar .nav-link,
        .cem-navbar .navbar-brand,
        .cem-navbar .navbar-text {
            color: #fff !important;
        }
        .cem-shell {
            min-height: calc(100vh - 72px);
        }
        .cem-card {
            border: 0;
            border-radius: 1.25rem;
            box-shadow: 0 18px 40px rgba(17, 50, 58, 0.08);
        }
        .cem-badge {
            background: rgba(28, 124, 108, 0.12);
            color: var(--cem-green);
            border: 1px solid rgba(28, 124, 108, 0.18);
        }
        .cem-card-header {
            background: linear-gradient(135deg, rgba(28, 124, 108, 0.1), rgba(216, 124, 77, 0.08));
            border-bottom: 1px solid rgba(23, 52, 59, 0.08);
        }
        .cem-soft {
            color: rgba(23, 52, 59, 0.7);
        }
        .cem-hero {
            background: linear-gradient(135deg, #10363a 0%, #1c7c6c 52%, #d87c4d 100%);
            color: white;
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: 0 24px 50px rgba(16, 54, 58, 0.18);
        }
        .cem-pill {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .35rem .8rem;
            border-radius: 999px;
            background: rgba(255,255,255,.16);
            color: white;
            font-size: .875rem;
        }
        .btn-cem {
            background: linear-gradient(135deg, #1c7c6c, #165e54);
            color: white;
            border: 0;
        }
        .btn-cem:hover {
            background: linear-gradient(135deg, #165e54, #124841);
            color: white;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg cem-navbar navbar-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">CEM Morondava</a>
        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#cemNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="cemNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}">Rapports</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('groups.index') }}">Discussions</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('notifications.index') }}">Notifications</a></li>
                @if(auth()->user()->isDirector())
                    <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">Utilisateurs</a></li>
                @endif
            </ul>
            <div class="d-flex align-items-center gap-3">
                <span class="navbar-text small text-end">
                    <strong>{{ auth()->user()->name }}</strong><br>
                    <span class="opacity-75 text-capitalize">{{ auth()->user()->role }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-light" type="submit">Déconnexion</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<main class="cem-shell py-4">
    <div class="container-fluid px-4">
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <strong>Veuillez corriger les erreurs suivantes :</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
