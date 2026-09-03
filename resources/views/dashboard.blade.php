@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="cem-hero mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="cem-pill mb-3">Plateforme de communication interne CEM</span>
            <h1 class="display-6 fw-bold mb-2">Bienvenue, {{ auth()->user()->name }}</h1>
            <p class="mb-0 opacity-75">Votre espace central pour suivre les rapports journaliers, coordonner les groupes et garder une traçabilité nette des échanges.</p>
        </div>
        <div class="text-end">
            <span class="badge rounded-pill bg-light text-dark text-capitalize px-3 py-2">{{ auth()->user()->role }}</span>
            <div class="mt-3 small opacity-75">{{ auth()->user()->position ?? 'Poste non renseigné' }}</div>
            <div class="small opacity-75">{{ auth()->user()->department ?? 'Département non renseigné' }}</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card cem-card h-100">
            <div class="card-body">
                <div class="cem-soft small">Utilisateurs</div>
                <div class="display-6 fw-bold">{{ $totalUsers }}</div>
                <div class="small cem-soft">Comptes présents dans le système</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card cem-card h-100">
            <div class="card-body">
                <div class="cem-soft small">Rapports</div>
                <div class="display-6 fw-bold">{{ $totalReports }}</div>
                <div class="small cem-soft">{{ $isDirector ? 'Tous les rapports' : 'Vos rapports envoyés' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card cem-card h-100">
            <div class="card-body">
                <div class="cem-soft small">Groupes</div>
                <div class="display-6 fw-bold">{{ $totalGroups }}</div>
                <div class="small cem-soft">Discussions actives</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card cem-card h-100">
            <div class="card-body">
                <div class="cem-soft small">Notifications non lues</div>
                <div class="display-6 fw-bold">{{ $unreadNotifications }}</div>
                <div class="small cem-soft">Alertes internes en attente</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card cem-card mb-4">
            <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
                <strong>Rapports récents</strong>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-cem">Ouvrir les rapports</a>
            </div>
            <div class="card-body">
                @forelse ($recentReports as $report)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <h5 class="mb-1">{{ $report->title }}</h5>
                                <div class="small cem-soft">Par {{ $report->user->name }} le {{ $report->submitted_at?->format('d/m/Y H:i') }}</div>
                            </div>
                            @if($report->attachment_path)
                                <a href="{{ route('reports.download', $report) }}" class="btn btn-outline-secondary btn-sm">Pièce jointe</a>
                            @endif
                        </div>
                        <p class="mt-2 mb-0">{{ IlluminateSupportStr::limit($report->content, 180) }}</p>
                    </div>
                @empty
                    <div class="text-center cem-soft py-4">Aucun rapport pour le moment.</div>
                @endforelse
            </div>
        </div>

        <div class="card cem-card">
            <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
                <strong>Derniers messages de groupe</strong>
                <a href="{{ route('groups.index') }}" class="btn btn-sm btn-cem">Voir les groupes</a>
            </div>
            <div class="card-body">
                @forelse ($recentMessages as $message)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <h6 class="mb-1">{{ $message->discussionGroup->name }}</h6>
                                <div class="small cem-soft">{{ $message->user->name }} - {{ $message->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <p class="mt-2 mb-0">{{ $message->content }}</p>
                    </div>
                @empty
                    <div class="text-center cem-soft py-4">Aucun message récent.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card cem-card mb-4">
            <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
                <strong>Groupes suivis</strong>
                <a href="{{ route('groups.index') }}" class="btn btn-sm btn-cem">Ouvrir</a>
            </div>
            <div class="card-body">
                @forelse ($recentGroups as $group)
                    <div class="border-bottom pb-3 mb-3">
                        <h6 class="mb-1">{{ $group->name }}</h6>
                        <div class="small cem-soft">Créé par {{ $group->creator->name }}</div>
                    </div>
                @empty
                    <div class="cem-soft">Aucun groupe accessible.</div>
                @endforelse
            </div>
        </div>

        <div class="card cem-card">
            <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
                <strong>Notifications</strong>
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-cem">Toutes</a>
            </div>
            <div class="card-body">
                @forelse ($notifications as $notification)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="fw-semibold">{{ $notification->title }}</div>
                                <div class="small cem-soft">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @if(! $notification->is_read)
                                <span class="badge cem-badge">Non lue</span>
                            @endif
                        </div>
                        <div class="small mt-2">{{ $notification->content }}</div>
                    </div>
                @empty
                    <div class="cem-soft">Aucune notification.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
