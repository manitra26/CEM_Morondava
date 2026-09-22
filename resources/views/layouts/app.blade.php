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
        .cem-sidebar .nav-link { padding: .75rem 1rem; border-radius: .75rem; } .cem-sidebar .nav-link:hover, .cem-sidebar .nav-link:focus-visible, .cem-sidebar .nav-link.is-active { background: rgba(255,255,255,.14); } @media (min-width: 992px) { .cem-sidebar { position: fixed; inset: 0 auto 0 0; z-index: 1030; width: 17rem; padding: 1.5rem 1rem; overflow-y: auto; } .cem-sidebar-inner { min-height: 100%; display: flex; flex-direction: column; align-items: stretch; } .cem-sidebar .navbar-collapse { display: flex !important; flex: 1 1 auto; flex-direction: column; align-items: stretch; width: 100%; } .cem-sidebar .navbar-nav { width: 100%; margin-right: 0 !important; } .cem-sidebar-profile { margin-top: auto; } .cem-shell { min-height: 100vh; margin-left: 17rem; } } @media (max-width: 991.98px) { .cem-sidebar { padding: .75rem 0; } .cem-sidebar-profile { padding-top: 1rem; } }
        .cem-navbar .nav-link,
        .cem-navbar .navbar-brand,
        .cem-navbar .navbar-text {
            color: #fff !important;
        }
        @media (min-width: 992px) { .cem-sidebar .navbar-nav { display: flex !important; flex-direction: column !important; gap: .35rem; margin-top: 1.25rem !important; margin-bottom: 0 !important; } .cem-sidebar .nav-item, .cem-sidebar .nav-link { width: 100%; } .cem-sidebar .nav-link { display: block; } .cem-sidebar-profile { order: -1; display: grid !important; grid-template-columns: auto minmax(0, 1fr); width: 100%; gap: .75rem !important; margin: 0 0 1.5rem; padding: 0 0 1.25rem; border-bottom: 1px solid rgba(255,255,255,.18); } .cem-sidebar-profile .navbar-text { min-width: 0; text-align: left !important; } .cem-sidebar-profile form { grid-column: 1 / -1; width: 100%; } .cem-sidebar-profile form .btn { width: 100%; } }
        .cem-sidebar-brand { width: 100%; } .cem-sidebar-logo-toggle { display: inline-flex; align-items: center; width: 100%; gap: .65rem; padding: 0; border: 0; background: transparent; color: #fff; text-align: left; } .cem-sidebar-logo-toggle:hover .cem-brand-logo, .cem-sidebar-logo-toggle:focus-visible .cem-brand-logo { transform: scale(1.06); box-shadow: 0 .45rem 1.1rem rgba(0,0,0,.2); } .cem-brand-logo { display: grid; place-items: center; width: 2.5rem; height: 2.5rem; flex: 0 0 2.5rem; border: 2px solid rgba(255,255,255,.8); border-radius: .8rem; background: linear-gradient(135deg, #d87c4d, #f1bd57); color: #10363a; font-size: .68rem; font-weight: 800; letter-spacing: -.04em; transition: transform .15s ease, box-shadow .15s ease; } .cem-brand-name { color: #fff; font-size: 1.15rem; font-weight: 700; } @media (min-width: 992px) { .cem-sidebar { transition: width .2s ease, padding .2s ease; } .cem-shell { transition: margin-left .2s ease; } .cem-sidebar-profile { order: initial; margin-top: auto; margin-bottom: 0; padding-top: 1.25rem; padding-bottom: 0; border-top: 1px solid rgba(255,255,255,.18); border-bottom: 0; } body.sidebar-collapsed .cem-sidebar { width: 6rem; padding-left: .75rem; padding-right: .75rem; } body.sidebar-collapsed .cem-shell { margin-left: 6rem; } body.sidebar-collapsed .cem-brand-name { display: none; } }
        html, body { max-width: 100%; overflow-x: hidden; } .cem-sidebar { overflow-x: hidden; } @media (min-width: 992px) { .cem-sidebar-brand { min-width: 0; } .cem-sidebar-logo-toggle { min-width: 0; } .cem-brand-name { white-space: nowrap; } body.sidebar-collapsed .cem-sidebar-brand, body.sidebar-collapsed .cem-sidebar-logo-toggle { display: flex; justify-content: center; } }
        .cem-nav-icon { display: inline-grid; place-items: center; width: 1.4rem; height: 1.4rem; flex: 0 0 1.4rem; color: currentColor; line-height: 1; } .cem-nav-icon svg, .cem-logout-icon svg { width: 1.25rem; height: 1.25rem; } .cem-nav-label { min-width: 0; } @media (min-width: 992px) { .cem-sidebar .nav-link { display: flex; align-items: center; gap: .75rem; } body.sidebar-collapsed .cem-sidebar-inner { padding-left: 0 !important; padding-right: 0 !important; } body.sidebar-collapsed .cem-sidebar .navbar-collapse { display: flex !important; } body.sidebar-collapsed .cem-sidebar .nav-item { display: flex; justify-content: center; } body.sidebar-collapsed .cem-sidebar .nav-link { position: relative; justify-content: center; width: 4rem !important; height: 4rem !important; min-height: 4rem; flex: 0 0 4rem; padding: 0; border-radius: 1rem !important; overflow: visible; } body.sidebar-collapsed .cem-sidebar .nav-link.is-active { border-radius: 1rem !important; } body.sidebar-collapsed .cem-sidebar .nav-link::after { content: attr(data-nav-label); position: absolute; left: calc(100% + .75rem); top: 50%; z-index: 1040; transform: translateY(-50%); padding: .4rem .65rem; border-radius: .45rem; background: #10363a; color: white; font-size: .78rem; font-weight: 600; white-space: nowrap; opacity: 0; pointer-events: none; transition: opacity .15s ease; box-shadow: 0 .5rem 1.25rem rgba(16,54,58,.2); } body.sidebar-collapsed .cem-sidebar .nav-link:hover::after, body.sidebar-collapsed .cem-sidebar .nav-link:focus-visible::after { opacity: 1; } body.sidebar-collapsed .cem-sidebar .cem-nav-label { display: none; } body.sidebar-collapsed .cem-sidebar-profile { display: block !important; width: 4rem; align-self: center; margin-top: auto; margin-left: 0; margin-right: 0; padding: .75rem 0 0; border: 0; } body.sidebar-collapsed .cem-sidebar-profile > img, body.sidebar-collapsed .cem-sidebar-profile > .navbar-text { display: none !important; } body.sidebar-collapsed .cem-sidebar-profile form, body.sidebar-collapsed .cem-sidebar-profile form .btn { display: grid; place-items: center; width: 4rem; height: 4rem; padding: 0; } body.sidebar-collapsed .cem-sidebar-profile form .btn { border-radius: 1rem; } body.sidebar-collapsed .cem-sidebar-profile .cem-logout-label { display: none; } }
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
        .group-members-grid { min-height: 0; flex: 1 1 auto; align-content: flex-start; overflow-y: auto; scrollbar-width: thin; }
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
        .password-field {
            position: relative;
        }
        .password-field .form-control {
            padding-right: 3rem;
        }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 0.85rem;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #5d6d73;
            display: grid;
            place-items: center;
            width: 2rem;
            height: 2rem;
            padding: 0;
            cursor: pointer;
        }
        .password-toggle:focus {
            outline: 2px solid rgba(28, 124, 108, 0.4);
            border-radius: 50%;
        }
        .password-toggle svg {
            width: 1.2rem;
            height: 1.2rem;
        }
        html.theme-dark .password-toggle {
            color: #9ab0ad;
        }
        .btn.member-profile-trigger,
        .member-profile-trigger,
        .cem-user-name,
        .cem-user-link {
            color: #0d6e8a !important;
            font-weight: 600;
            text-decoration: none;
            box-shadow: none !important;
            transition: color .15s ease, text-decoration .15s ease;
        }
        .btn.member-profile-trigger:hover,
        .btn.member-profile-trigger:focus-visible,
        .member-profile-trigger:hover,
        .member-profile-trigger:focus-visible,
        .cem-user-name:hover,
        .cem-user-link:hover {
            color: #084c61 !important;
            text-decoration: underline !important;
        }
        html.theme-dark .btn.member-profile-trigger,
        html.theme-dark .member-profile-trigger,
        html.theme-dark .cem-user-name,
        html.theme-dark .cem-user-link {
            color: #2dd4bf !important;
        }
        html.theme-dark .btn.member-profile-trigger:hover,
        html.theme-dark .btn.member-profile-trigger:focus-visible,
        html.theme-dark .member-profile-trigger:hover,
        html.theme-dark .member-profile-trigger:focus-visible,
        html.theme-dark .cem-user-name:hover,
        html.theme-dark .cem-user-link:hover {
            color: #5eead4 !important;
        }
        .dashboard-scroll {
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-width: thin;
            scrollbar-color: rgba(28, 124, 108, 0.35) transparent;
        }
        .dashboard-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .dashboard-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .dashboard-scroll::-webkit-scrollbar-thumb {
            background: rgba(28, 124, 108, 0.35);
            border-radius: 999px;
        }
        .dashboard-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(28, 124, 108, 0.6);
        }
        .dashboard-scroll > div:last-child {
            border-bottom: 0 !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
        html.theme-dark .dashboard-scroll::-webkit-scrollbar-thumb {
            background: rgba(45, 212, 191, 0.35);
        }
        .cem-stat-card {
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .cem-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px rgba(17, 50, 58, 0.12);
        }
        html.theme-dark .cem-stat-card:hover {
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.35);
        }
        .cem-stat-icon {
            width: 2.75rem;
            height: 2.75rem;
            flex: 0 0 2.75rem;
            border-radius: 0.85rem;
            display: grid;
            place-items: center;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg cem-navbar cem-sidebar navbar-dark">
    <div class="container-fluid px-4 cem-sidebar-inner">
        <div class="cem-sidebar-brand">
            <button id="cem-sidebar-toggle" class="cem-sidebar-logo-toggle" type="button" aria-label="Masquer la sidebar" aria-expanded="true">
                <span class="cem-brand-logo" aria-hidden="true">CEM</span>
                <span class="cem-brand-name">CEM Morondava</span>
            </button>
        </div>
        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#cemNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="cemNav">
            <ul class="navbar-nav cem-sidebar-links me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" data-nav-label="Tableau de bord" href="{{ route('dashboard') }}"><span class="cem-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7"/><path d="M5 9v11h14V9"/><path d="M9 20v-6h6v6"/></svg></span><span class="cem-nav-label">Tableau de bord</span></a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('reports.*') ? 'is-active' : '' }}" data-nav-label="Rapports" href="{{ route('reports.index') }}"><span class="cem-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 7h8M8 11h8M8 15h5"/></svg></span><span class="cem-nav-label">Rapports</span></a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('groups.*') ? 'is-active' : '' }}" data-nav-label="Discussions" href="{{ route('groups.index') }}"><span class="cem-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9.6 9.6 0 0 1-4.2-1L3 20l1.5-4.1A8.5 8.5 0 1 1 21 11.5Z"/><path d="M8 11h.01M12 11h.01M16 11h.01"/></svg></span><span class="cem-nav-label">Discussions</span></a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('private.messages.*') ? 'is-active' : '' }}" data-nav-label="Messages" href="{{ route('private.messages.index') }}"><span class="cem-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span><span class="cem-nav-label">Messages</span></a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('notifications.*') ? 'is-active' : '' }}" data-nav-label="Notifications" href="{{ route('notifications.index') }}"><span class="cem-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg></span><span class="cem-nav-label">Notifications</span></a></li>
                @if(auth()->user()->isDirector())
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('users.*') ? 'is-active' : '' }}" data-nav-label="Utilisateurs" href="{{ route('users.index') }}"><span class="cem-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8M17 11a4 4 0 0 0 0-8M21 21v-2a4 4 0 0 0-3-3.87"/></svg></span><span class="cem-nav-label">Utilisateurs</span></a></li>
                @endif
            </ul>
            <div class="cem-sidebar-profile d-flex align-items-center gap-3">
                @if(auth()->user()->avatar_path)
                    <img src="{{ route('profile.avatar', auth()->user()) }}" alt="Photo de profil" class="cem-avatar cem-avatar-nav">
                @else
                    <span class="cem-avatar cem-avatar-nav cem-avatar-placeholder">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                @endif
                <span class="navbar-text small text-end">
                    <a href="#header-profile-modal" class="text-white text-decoration-none" data-bs-toggle="modal"><strong>{{ auth()->user()->name }}</strong></a><br>
                    <span class="opacity-75 text-capitalize">{{ auth()->user()->role }}</span><br><a href="{{ route('profile.edit') }}" class="small text-white">Profil et paramètres</a>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-light d-flex align-items-center justify-content-center gap-2" type="submit" aria-label="Déconnexion"><span class="cem-logout-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17l5-5-5-5M15 12H3M21 19V5a2 2 0 0 0-2-2h-6"/></svg></span><span class="cem-logout-label">Déconnexion</span></button>
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

<div class="modal fade" id="header-profile-modal" tabindex="-1" aria-labelledby="header-profile-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content cem-card">
            <div class="modal-header cem-card-header">
                <h2 class="modal-title h5 mb-0" id="header-profile-modal-title">Mon profil</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if(auth()->user()->avatar_path)
                        <img src="{{ route('profile.avatar', auth()->user()) }}" alt="Photo de {{ auth()->user()->name }}" class="cem-avatar cem-avatar-lg">
                    @else
                        <span class="cem-avatar cem-avatar-lg cem-avatar-placeholder">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    @endif
                    <div class="min-w-0">
                        <h3 class="h4 mb-1 text-truncate">{{ auth()->user()->name }}</h3>
                        <span class="badge cem-badge text-capitalize">{{ auth()->user()->role }}</span>
                        <div class="cem-soft mt-2">{{ auth()->user()->position ?: 'Poste non renseigné' }}</div>
                    </div>
                </div>
                <p class="mb-3">{{ auth()->user()->bio ?: 'Aucune biographie renseignée.' }}</p>
                <div class="row g-2">
                    <div class="col-6"><div class="cem-info-box"><span>Département</span><strong>{{ auth()->user()->department ?: 'Non renseigné' }}</strong></div></div>
                    <div class="col-6"><div class="cem-info-box"><span>Domicile</span><strong>{{ auth()->user()->domicile ?: 'Non renseigné' }}</strong></div></div>
                    <div class="col-6"><div class="cem-info-box"><span>Téléphone</span><strong>{{ auth()->user()->phone ?: 'Non renseigné' }}</strong></div></div>
                    <div class="col-6"><div class="cem-info-box"><span>Email</span><strong class="text-break">{{ auth()->user()->email }}</strong></div></div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('profile.edit') }}" class="btn btn-cem">Modifier mon profil</a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
<script>
    (() => {
        const preference = @json(auth()->user()->theme ?? 'system');
        const dark = preference === 'dark' || (preference === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('theme-dark', dark);
    })();
</script>

<script>(() => { const button = document.querySelector('#cem-sidebar-toggle'); if (!button) return; const apply = (collapsed) => { document.body.classList.toggle('sidebar-collapsed', collapsed); button.setAttribute('aria-expanded', String(!collapsed)); button.setAttribute('aria-label', collapsed ? 'Afficher la sidebar' : 'Masquer la sidebar'); }; apply(localStorage.getItem('cem-sidebar-collapsed') === 'true'); button.addEventListener('click', () => { const collapsed = !document.body.classList.contains('sidebar-collapsed'); apply(collapsed); localStorage.setItem('cem-sidebar-collapsed', String(collapsed)); }); })();</script>
<script>
    document.addEventListener('click', function (e) {
        const button = e.target.closest('[data-password-toggle]');
        if (!button) return;
        const field = button.closest('.password-field');
        if (!field) return;
        const input = field.querySelector('input');
        if (!input) return;
        const eyeOpen = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        `;
        const eyeClosed = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 3l18 18"></path>
                <path d="M10.58 10.58A2 2 0 0 0 13.42 13.42"></path>
                <path d="M9.88 5.08A10.94 10.94 0 0 1 12 5c6.5 0 10 7 10 7a16.77 16.77 0 0 1-4.21 5.32"></path>
                <path d="M6.61 6.61A16.8 16.8 0 0 0 2 12s3.5 7 10 7a11.17 11.17 0 0 0 5.39-1.61"></path>
            </svg>
        `;
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        button.innerHTML = isHidden ? eyeClosed : eyeOpen;
        button.setAttribute('aria-label', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        button.setAttribute('title', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
    });
</script>
</body>
</html>
