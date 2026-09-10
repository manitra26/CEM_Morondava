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

        .cem-avatar { width: 3rem; height: 3rem; border-radius: 50%; object-fit: cover; }
        .cem-avatar-lg { width: 7rem; height: 7rem; border: .35rem solid white; box-shadow: 0 8px 20px rgba(0,0,0,.15); }
        .cem-avatar-placeholder { display: grid; place-items: center; background: linear-gradient(135deg, #1c7c6c, #d87c4d); color: white; font-size: 2.5rem; font-weight: 700; }
        .cem-profile-cover { height: 9rem; background: linear-gradient(135deg, #10363a, #1c7c6c 55%, #d87c4d); }
        .cem-info-box { display: flex; flex-direction: column; gap: .25rem; padding: 1rem; border-radius: 1rem; background: rgba(28,124,108,.07); }
        .cem-info-box span { color: rgba(23,52,59,.65); font-size: .85rem; }
        .typing-dots { display: inline-flex; gap: .2rem; align-items: center; }
        .typing-dots i { width: .35rem; height: .35rem; border-radius: 50%; background: currentColor; animation: cem-bounce 1s infinite ease-in-out; }
        .typing-dots i:nth-child(2) { animation-delay: .15s; }
        .typing-dots i:nth-child(3) { animation-delay: .3s; }
        @keyframes cem-bounce { 0%, 60%, 100% { transform: translateY(0); opacity: .45; } 30% { transform: translateY(-.25rem); opacity: 1; } }
        html.theme-dark body { background: #142427; color: #edf7f3; }
        html.theme-dark .cem-card, html.theme-dark .bg-white { background: #203337 !important; color: #edf7f3; }
        html.theme-dark .cem-soft, html.theme-dark .cem-info-box span { color: rgba(237,247,243,.68); }
        html.theme-dark .form-control, html.theme-dark .form-select { background: #172a2d; border-color: #476165; color: #edf7f3; }
        html.theme-dark .list-group-item { background: transparent; color: #edf7f3; border-color: rgba(237,247,243,.12); }
        html.theme-dark .cem-info-box { background: rgba(255,255,255,.08); }
        html.theme-dark .btn-outline-secondary { color: #edf7f3; border-color: #9ab0ad; }
        .cem-avatar-nav { width: 2.75rem; height: 2.75rem; border: 2px solid rgba(255,255,255,.8); font-size: 1.1rem; }
        .cem-avatar-message { width: 2.75rem; height: 2.75rem; flex: 0 0 2.75rem; }
        .cem-member-avatar { width: 2.5rem; height: 2.5rem; flex: 0 0 2.5rem; }
        .cem-user-meta { font-size: .8rem; color: rgba(23,52,59,.62); }
        .reaction-picker form { display: inline-block; }
        .reaction-button { border-radius: 999px !important; min-width: 2.25rem; }
        .reaction-summary { background: rgba(28,124,108,.12); color: var(--cem-green); border: 1px solid rgba(28,124,108,.2); }
        .reaction-selected { background: rgba(216,124,77,.2); border-color: var(--cem-accent); }
        .cem-reply-quote { border-left: 3px solid var(--cem-accent); padding: .5rem .75rem; background: rgba(216,124,77,.08); color: rgba(23,52,59,.75); border-radius: .35rem; }
        .cem-group-avatar { width: 4rem; height: 4rem; flex: 0 0 4rem; }
        .groups-page { min-height: calc(100vh - 8rem); }
        .groups-list-card { display: flex; flex-direction: column; max-height: calc(100vh - 11rem); }
        .groups-list-card .card-header { flex: 0 0 auto; }
        .groups-list-scroll { min-height: 0; overflow-y: auto; overscroll-behavior: contain; scrollbar-width: thin; }
        .group-list-item:last-child { margin-bottom: 0 !important; }
        .groups-members-scroll { max-height: 28vh; overflow-y: auto; scrollbar-width: thin; }
        .group-chat-card { height: calc(100vh - 17rem); min-height: 28rem; }
        .group-chat-scroll { min-height: 0; overflow-y: auto; overscroll-behavior: contain; scrollbar-width: thin; }
        #group-info-modal .modal-dialog { width: calc(100% - 2rem); max-width: 75rem; height: calc(100vh - 2rem); margin-left: auto; margin-right: auto; }
        #group-info-modal .modal-content { height: 100%; }
        .group-info-body { min-height: 0; overflow: hidden; display: flex; flex-direction: column; }
        .group-info-panel { animation: cem-panel-in .18s ease-out; }
        .group-member-list { min-height: 0; flex: 1 1 auto; overflow-y: auto; scrollbar-width: thin; padding-right: .25rem; }
        #group-manage-members-panel:not(.d-none) { display: flex !important; flex-direction: column; min-height: 0; flex: 1 1 auto; }
        #group-members-panel:not(.d-none) { display: flex !important; flex-direction: column; min-height: 0; flex: 1 1 auto; }
        .group-members-grid { min-height: 0; flex: 1 1 auto; overflow-y: auto; scrollbar-width: thin; }
        .member-search-box { position: relative; width: 100%; max-width: 50rem; margin-inline: auto; }
        .member-search-box .form-control { padding-left: 1rem; padding-right: 2.5rem; border: 2px solid rgba(28,124,108,.3); border-radius: 999px; box-shadow: 0 5px 16px rgba(23,52,59,.06); }
        .member-search-box .form-control:focus { border-color: var(--cem-green); box-shadow: 0 0 0 .2rem rgba(28,124,108,.14); }
        .member-search-clear { position: absolute; z-index: 2; right: .65rem; top: 50%; transform: translateY(-50%); border: 0; background: transparent; color: var(--cem-soft); font-size: 1.35rem; line-height: 1; }
        .member-search-clear:hover { color: var(--cem-ink); }
        @keyframes cem-panel-in { from { opacity: .3; transform: translateY(.25rem); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 991.98px) {
            #group-info-modal .modal-dialog { height: calc(100vh - 1rem); margin: .5rem; }
            .group-chat-card { height: auto; min-height: 0; }
            .group-chat-scroll { max-height: 68vh; }
            .group-info-body { min-height: 0; }
        }
        @media (min-width: 992px) {
            .groups-page { display: grid; grid-template-columns: minmax(0, 1fr); }
            .groups-members-card { max-width: 32rem; }
        }
        @media (max-width: 991.98px) {
            .groups-list-card { max-height: none; }
            .groups-list-scroll { max-height: 62vh; }
        }
        .reports-page { min-height: calc(100vh - 8rem); }
        .reports-history-card { display: flex; flex-direction: column; max-height: calc(100vh - 11rem); }
        .reports-history-card .card-header { flex: 0 0 auto; }
        .reports-history-scroll { min-height: 0; overflow-y: auto; overscroll-behavior: contain; scrollbar-width: thin; }
        .report-history-item:last-child { margin-bottom: 0 !important; }
        @media (max-width: 991.98px) {
            .reports-history-card { max-height: none; }
            .reports-history-scroll { max-height: 62vh; }
        }
        .reaction-actions { min-height: 2.4rem; }
        .reaction-picker { position: absolute; z-index: 10; bottom: calc(100% + .45rem); left: 0; padding: .3rem; border-radius: 999px; background: white; box-shadow: 0 8px 25px rgba(23,52,59,.18); }
        .reaction-picker .reaction-button { border-radius: 999px !important; font-size: 1.25rem; min-width: 2.5rem; }
        html.theme-dark .reaction-picker { background: #203337; }
        .reaction-details-popover { position: absolute; z-index: 20; bottom: calc(100% + .45rem); left: 0; min-width: 18rem; max-width: 24rem; padding: .9rem; border-radius: 1rem; background: white; box-shadow: 0 10px 30px rgba(23,52,59,.22); }
        .reaction-details-trigger { border: 1px solid rgba(28,124,108,.2); cursor: pointer; }
        html.theme-dark .reaction-details-popover { background: #203337; color: #edf7f3; }
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
                @if(auth()->user()->avatar_path)
                    <img src="{{ route('profile.avatar', auth()->user()) }}" alt="Photo de profil" class="cem-avatar cem-avatar-nav">
                @else
                    <span class="cem-avatar cem-avatar-nav cem-avatar-placeholder">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                @endif
                <span class="navbar-text small text-end">
                    <a href="{{ route('profile.show', auth()->user()) }}" class="text-white text-decoration-none"><strong>{{ auth()->user()->name }}</strong></a><br>
                    <span class="opacity-75 text-capitalize">{{ auth()->user()->role }}</span><br><a href="{{ route('profile.edit') }}" class="small text-white">Profil et paramètres</a>
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
@stack('scripts')
<script>
    (() => {
        const preference = @json(auth()->user()->theme ?? 'system');
        const dark = preference === 'dark' || (preference === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('theme-dark', dark);
    })();
</script>

</body>
</html>
