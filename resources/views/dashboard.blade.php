@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="cem-hero mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="cem-pill mb-3">Plateforme de communication interne CEM</span>
            <h1 class="display-6 fw-bold mb-2">Bienvenue, {{ auth()->user()->name }}</h1>
            <p class="mb-0 opacity-75">Votre espace central pour suivre les rapports journaliers, coordonner les groupes et garder une traçabilité nette des échanges.</p>
            <div class="mt-3 d-flex gap-2 flex-wrap align-items-center">
                <a href="{{ route('reports.index') }}" class="btn btn-light text-dark fw-semibold rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Envoyer un rapport</span>
                </a>
                <a href="{{ route('groups.index') }}" class="btn btn-outline-light rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9.6 9.6 0 0 1-4.2-1L3 20l1.5-4.1A8.5 8.5 0 1 1 21 11.5Z"/></svg>
                    <span>Discussions de groupe</span>
                </a>
            </div>
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
        <a href="{{ auth()->user()->isDirector() ? route('users.index') : '#' }}" class="text-decoration-none text-reset">
            <div class="card cem-card cem-stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div class="cem-soft small fw-medium">Utilisateurs</div>
                        <div class="display-6 fw-bold mt-1">{{ $totalUsers }}</div>
                        <div class="small cem-soft mt-1">Comptes présents dans le système</div>
                    </div>
                    <div class="cem-stat-icon" style="background: rgba(13, 110, 138, 0.12); color: #0d6e8a;" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8M17 11a4 4 0 0 0 0-8M21 21v-2a4 4 0 0 0-3-3.87"/></svg>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reports.index') }}" class="text-decoration-none text-reset">
            <div class="card cem-card cem-stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div class="cem-soft small fw-medium">Rapports</div>
                        <div class="display-6 fw-bold mt-1">{{ $totalReports }}</div>
                        <div class="small cem-soft mt-1">{{ $isDirector ? 'Tous les rapports' : 'Vos rapports envoyés' }}</div>
                    </div>
                    <div class="cem-stat-icon" style="background: rgba(28, 124, 108, 0.12); color: #1c7c6c;" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 7h8M8 11h8M8 15h5"/></svg>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('groups.index') }}" class="text-decoration-none text-reset">
            <div class="card cem-card cem-stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div class="cem-soft small fw-medium">Groupes</div>
                        <div class="display-6 fw-bold mt-1">{{ $totalGroups }}</div>
                        <div class="small cem-soft mt-1">Discussions actives</div>
                    </div>
                    <div class="cem-stat-icon" style="background: rgba(216, 124, 77, 0.12); color: #d87c4d;" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9.6 9.6 0 0 1-4.2-1L3 20l1.5-4.1A8.5 8.5 0 1 1 21 11.5Z"/><path d="M8 11h.01M12 11h.01M16 11h.01"/></svg>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('notifications.index') }}" class="text-decoration-none text-reset">
            <div class="card cem-card cem-stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div class="cem-soft small fw-medium">Notifications non lues</div>
                        <div class="display-6 fw-bold mt-1">{{ $unreadNotifications }}</div>
                        <div class="small cem-soft mt-1">Alertes internes en attente</div>
                    </div>
                    <div class="cem-stat-icon" style="background: rgba(241, 189, 87, 0.15); color: #c4821c;" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card cem-card mb-4 mb-lg-0">
            <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
                <strong>Rapports récents</strong>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-cem">Ouvrir les rapports</a>
            </div>
            <div class="card-body dashboard-scroll" style="max-height: 33rem;">
                @forelse ($recentReports as $report)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between gap-3">
                            <div class="min-w-0 flex-grow-1">
                                <h5 class="mb-1 text-truncate">{{ $report->title }}</h5>
                                <div class="small cem-soft d-flex align-items-center gap-2 flex-wrap">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @if($report->user->avatar_path)
                                            <img src="{{ route('profile.avatar', $report->user) }}" alt="" class="cem-avatar" style="width: 1.6rem; height: 1.6rem;">
                                        @else
                                            <span class="cem-avatar cem-avatar-placeholder" style="width: 1.6rem; height: 1.6rem; font-size: 0.75rem;">{{ strtoupper(substr($report->user->name, 0, 1)) }}</span>
                                        @endif
                                        <button type="button" class="btn btn-link p-0 border-0 align-baseline text-decoration-none fw-semibold member-profile-trigger" data-member-name="{{ $report->user->name }}" data-member-role="{{ ucfirst($report->user->role) }}" data-member-position="{{ $report->user->position }}" data-member-department="{{ $report->user->department }}" data-member-domicile="{{ $report->user->domicile }}" data-member-phone="{{ $report->user->phone }}" data-member-email="{{ $report->user->email }}" data-member-bio="{{ $report->user->bio }}" data-member-avatar="{{ $report->user->avatar_path ? route('profile.avatar', $report->user) : '' }}" data-member-initial="{{ strtoupper(substr($report->user->name, 0, 1)) }}" data-member-message-url="{{ route('private.messages.user', $report->user) }}" data-member-is-current="{{ $report->user->id === auth()->id() ? '1' : '0' }}">{{ $report->user->name }}</button>
                                    </div>
                                    <span>&bull;</span>
                                    <span>
                                        @if($report->submitted_at?->isToday())
                                            Aujourd'hui à {{ $report->submitted_at->format('H:i') }}
                                        @elseif($report->submitted_at?->isYesterday())
                                            Hier à {{ $report->submitted_at->format('H:i') }}
                                        @else
                                            {{ $report->submitted_at?->format('d/m/Y H:i') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            @if($report->attachment_path)
                                <a href="{{ route('reports.download', $report) }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1 flex-shrink-0 align-self-start">
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                    <span>Pièce jointe</span>
                                </a>
                            @endif
                        </div>
                        <p class="mt-2 mb-0">{{ \Illuminate\Support\Str::limit($report->content, 180) }}</p>
                    </div>
                @empty
                    <div class="text-center cem-soft py-4">Aucun rapport pour le moment.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card cem-card mb-4">
            <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
                <strong>Groupes suivis</strong>
                <a href="{{ route('groups.index') }}" class="btn btn-sm btn-cem">Ouvrir</a>
            </div>
            <div class="card-body dashboard-scroll" style="max-height: 14rem;">
                @forelse ($recentGroups as $group)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="cem-stat-icon" style="width: 2.3rem; height: 2.3rem; flex: 0 0 2.3rem; border-radius: 0.65rem; background: rgba(28, 124, 108, 0.1); color: #1c7c6c;" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <h6 class="mb-0 text-truncate">{{ $group->name }}</h6>
                                <div class="small cem-soft">Créé par <button type="button" class="btn btn-link p-0 border-0 align-baseline text-decoration-none fw-semibold member-profile-trigger" data-member-name="{{ $group->creator->name }}" data-member-role="{{ ucfirst($group->creator->role) }}" data-member-position="{{ $group->creator->position }}" data-member-department="{{ $group->creator->department }}" data-member-domicile="{{ $group->creator->domicile }}" data-member-phone="{{ $group->creator->phone }}" data-member-email="{{ $group->creator->email }}" data-member-bio="{{ $group->creator->bio }}" data-member-avatar="{{ $group->creator->avatar_path ? route('profile.avatar', $group->creator) : '' }}" data-member-initial="{{ strtoupper(substr($group->creator->name, 0, 1)) }}" data-member-message-url="{{ route('private.messages.user', $group->creator) }}" data-member-is-current="{{ $group->creator->id === auth()->id() ? '1' : '0' }}">{{ $group->creator->name }}</button></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="cem-soft">Aucun groupe accessible.</div>
                @endforelse
            </div>
        </div>

        <div class="card cem-card">
            <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
                <strong>Derniers messages de groupe</strong>
                <a href="{{ route('groups.index') }}" class="btn btn-sm btn-cem">Voir les groupes</a>
            </div>
            <div class="card-body dashboard-scroll" style="max-height: 15.5rem;">
                @forelse ($recentMessages as $message)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex align-items-start gap-2">
                            @if($message->user->avatar_path)
                                <img src="{{ route('profile.avatar', $message->user) }}" alt="" class="cem-avatar mt-1" style="width: 1.75rem; height: 1.75rem; flex: 0 0 1.75rem;">
                            @else
                                <span class="cem-avatar cem-avatar-placeholder mt-1" style="width: 1.75rem; height: 1.75rem; flex: 0 0 1.75rem; font-size: 0.75rem;">{{ strtoupper(substr($message->user->name, 0, 1)) }}</span>
                            @endif
                            <div class="min-w-0 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-baseline gap-2">
                                    <span class="badge text-bg-light border small text-truncate" style="max-width: 12rem;">{{ $message->discussionGroup->name }}</span>
                                    <span class="small cem-soft" style="font-size: 0.75rem;">
                                        @if($message->created_at->isToday())
                                            {{ $message->created_at->format('H:i') }}
                                        @elseif($message->created_at->isYesterday())
                                            Hier {{ $message->created_at->format('H:i') }}
                                        @else
                                            {{ $message->created_at->format('d/m H:i') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="mt-1">
                                    <button type="button" class="btn btn-link p-0 border-0 align-baseline text-decoration-none fw-semibold member-profile-trigger" data-member-name="{{ $message->user->name }}" data-member-role="{{ ucfirst($message->user->role) }}" data-member-position="{{ $message->user->position }}" data-member-department="{{ $message->user->department }}" data-member-domicile="{{ $message->user->domicile }}" data-member-phone="{{ $message->user->phone }}" data-member-email="{{ $message->user->email }}" data-member-bio="{{ $message->user->bio }}" data-member-avatar="{{ $message->user->avatar_path ? route('profile.avatar', $message->user) : '' }}" data-member-initial="{{ strtoupper(substr($message->user->name, 0, 1)) }}" data-member-message-url="{{ route('private.messages.user', $message->user) }}" data-member-is-current="{{ $message->user->id === auth()->id() ? '1' : '0' }}">{{ $message->user->name }}</button>
                                    <p class="mt-1 mb-0 small text-break opacity-90">{{ $message->content }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center cem-soft py-4">Aucun message récent.</div>
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
