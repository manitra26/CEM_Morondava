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
                                <div class="small cem-soft">Par <button type="button" class="btn btn-link p-0 border-0 align-baseline text-decoration-none fw-semibold member-profile-trigger" data-member-name="{{ $report->user->name }}" data-member-role="{{ ucfirst($report->user->role) }}" data-member-position="{{ $report->user->position }}" data-member-department="{{ $report->user->department }}" data-member-domicile="{{ $report->user->domicile }}" data-member-phone="{{ $report->user->phone }}" data-member-email="{{ $report->user->email }}" data-member-bio="{{ $report->user->bio }}" data-member-avatar="{{ $report->user->avatar_path ? route('profile.avatar', $report->user) : '' }}" data-member-initial="{{ strtoupper(substr($report->user->name, 0, 1)) }}" data-member-message-url="{{ route('private.messages.user', $report->user) }}" data-member-is-current="{{ $report->user->id === auth()->id() ? '1' : '0' }}">{{ $report->user->name }}</button> le {{ $report->submitted_at?->format('d/m/Y H:i') }}</div>
                            </div>
                            @if($report->attachment_path)
                                <a href="{{ route('reports.download', $report) }}" class="btn btn-outline-secondary btn-sm">Pièce jointe</a>
                            @endif
                        </div>
                        <p class="mt-2 mb-0">{{ \Illuminate\Support\Str::limit($report->content, 180) }}</p>
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
                                <div class="small cem-soft"><button type="button" class="btn btn-link p-0 border-0 align-baseline text-decoration-none fw-semibold member-profile-trigger" data-member-name="{{ $message->user->name }}" data-member-role="{{ ucfirst($message->user->role) }}" data-member-position="{{ $message->user->position }}" data-member-department="{{ $message->user->department }}" data-member-domicile="{{ $message->user->domicile }}" data-member-phone="{{ $message->user->phone }}" data-member-email="{{ $message->user->email }}" data-member-bio="{{ $message->user->bio }}" data-member-avatar="{{ $message->user->avatar_path ? route('profile.avatar', $message->user) : '' }}" data-member-initial="{{ strtoupper(substr($message->user->name, 0, 1)) }}" data-member-message-url="{{ route('private.messages.user', $message->user) }}" data-member-is-current="{{ $message->user->id === auth()->id() ? '1' : '0' }}">{{ $message->user->name }}</button> - {{ $message->created_at->format('d/m/Y H:i') }}</div>
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
                        <div class="small cem-soft">Créé par <button type="button" class="btn btn-link p-0 border-0 align-baseline text-decoration-none fw-semibold member-profile-trigger" data-member-name="{{ $group->creator->name }}" data-member-role="{{ ucfirst($group->creator->role) }}" data-member-position="{{ $group->creator->position }}" data-member-department="{{ $group->creator->department }}" data-member-domicile="{{ $group->creator->domicile }}" data-member-phone="{{ $group->creator->phone }}" data-member-email="{{ $group->creator->email }}" data-member-bio="{{ $group->creator->bio }}" data-member-avatar="{{ $group->creator->avatar_path ? route('profile.avatar', $group->creator) : '' }}" data-member-initial="{{ strtoupper(substr($group->creator->name, 0, 1)) }}" data-member-message-url="{{ route('private.messages.user', $group->creator) }}" data-member-is-current="{{ $group->creator->id === auth()->id() ? '1' : '0' }}">{{ $group->creator->name }}</button></div>
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

<div class="modal fade" id="member-profile-modal" tabindex="-1" aria-labelledby="member-profile-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content cem-card">
            <div class="modal-header cem-card-header">
                <h2 class="modal-title h5 mb-0" id="member-profile-modal-title">Profil du membre</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img id="member-profile-avatar" src="" alt="" class="cem-avatar cem-avatar-lg d-none">
                    <span id="member-profile-initial" class="cem-avatar cem-avatar-lg cem-avatar-placeholder"></span>
                    <div class="min-w-0">
                        <h3 id="member-profile-name" class="h4 mb-1 text-truncate"></h3>
                        <span id="member-profile-role" class="badge cem-badge"></span>
                        <div id="member-profile-position" class="cem-soft mt-2"></div>
                    </div>
                </div>
                <p id="member-profile-bio" class="mb-3"></p>
                <div class="row g-2">
                    <div class="col-6"><div class="cem-info-box"><span>Département</span><strong id="member-profile-department"></strong></div></div>
                    <div class="col-6"><div class="cem-info-box"><span>Domicile</span><strong id="member-profile-domicile"></strong></div></div>
                    <div class="col-6"><div class="cem-info-box"><span>Téléphone</span><strong id="member-profile-phone"></strong></div></div>
                    <div class="col-6"><div class="cem-info-box"><span>Email</span><strong id="member-profile-email" class="text-break"></strong></div></div>
                </div>
            </div>
            <div class="modal-footer">
                <a id="member-profile-message" href="#" class="btn btn-cem">Message</a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const modal = document.querySelector('#member-profile-modal');
    if (!modal) return;

    const avatar = document.querySelector('#member-profile-avatar');
    const initial = document.querySelector('#member-profile-initial');
    const name = document.querySelector('#member-profile-name');
    const role = document.querySelector('#member-profile-role');
    const position = document.querySelector('#member-profile-position');
    const bio = document.querySelector('#member-profile-bio');
    const department = document.querySelector('#member-profile-department');
    const domicile = document.querySelector('#member-profile-domicile');
    const phone = document.querySelector('#member-profile-phone');
    const email = document.querySelector('#member-profile-email');
    const message = document.querySelector('#member-profile-message');

    document.querySelectorAll('.member-profile-trigger').forEach((button) => {
        button.addEventListener('click', () => {
            const member = button.dataset;
            name.textContent = member.memberName;
            role.textContent = member.memberRole;
            position.textContent = member.memberPosition || 'Poste non renseigné';
            bio.textContent = member.memberBio || 'Aucune biographie renseignée.';
            department.textContent = member.memberDepartment || 'Non renseigné';
            domicile.textContent = member.memberDomicile || 'Non renseigné';
            phone.textContent = member.memberPhone || 'Non renseigné';
            email.textContent = member.memberEmail || 'Non renseigné';
            initial.textContent = member.memberInitial;
            initial.classList.toggle('d-none', Boolean(member.memberAvatar));
            avatar.classList.toggle('d-none', !member.memberAvatar);
            avatar.src = member.memberAvatar || '';
            avatar.alt = member.memberAvatar ? 'Photo de ' + member.memberName : '';
            message.href = member.memberMessageUrl;
            message.classList.toggle('d-none', member.memberIsCurrent === '1');
            bootstrap.Modal.getOrCreateInstance(modal).show();
        });
    });
})();
</script>
@endpush

@endsection
