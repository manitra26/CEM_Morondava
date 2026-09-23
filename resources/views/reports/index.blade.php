@extends('layouts.app')

@section('title', 'Rapports journaliers')

@section('content')
<div class="reports-page">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Rapports journaliers</h1>
            <p class="cem-soft mb-0">Envoyez vos rapports et retrouvez facilement l'historique.</p>
        </div>
        <button type="button" class="btn btn-cem px-4" data-bs-toggle="modal" data-bs-target="#report-create-modal">
            + Envoyer un rapport
        </button>
    </div>

    <div class="card cem-card reports-history-card">
        <div class="card-header cem-card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <strong>Historique des rapports</strong>
                <span class="badge cem-badge">{{ $reports->count() }} rapport(s)</span>
            </div>
            <form method="GET" action="{{ route('reports.index') }}" class="reports-filter-form">
                <div class="row g-2 align-items-end">
                    <div class="col-xl-5 col-lg-4">
                        <label for="report-search" class="form-label small mb-1">Rechercher</label>
                        <input id="report-search" type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Titre, contenu ou auteur">
                    </div>
                    @if($isDirector)
                        <div class="col-xl-3 col-lg-3">
                            <label for="report-user" class="form-label small mb-1">Utilisateur</label>
                            <select id="report-user" name="user_id" class="form-select">
                                <option value="">Tous les utilisateurs</option>
                                @foreach($reportUsers as $reportUser)
                                    <option value="{{ $reportUser->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $reportUser->id)>{{ $reportUser->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-xl-2 col-lg-2 col-sm-6">
                        <label for="report-date-from" class="form-label small mb-1">Du</label>
                        <input id="report-date-from" type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div class="col-xl-2 col-lg-2 col-sm-6">
                        <label for="report-date-to" class="form-label small mb-1">Au</label>
                        <input id="report-date-to" type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-cem">Filtrer</button>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body reports-history-scroll">
            @forelse ($reports as $report)
                <article class="border rounded-4 p-3 mb-3 bg-white report-history-item">
                    <div class="d-flex justify-content-between gap-3 flex-wrap">
                        <div>
                            <h5 class="mb-1">{{ $report->title }}</h5>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                @if($report->user->avatar_path)
                                    <img src="{{ route('profile.avatar', $report->user) }}" alt="Photo de {{ $report->user->name }}" class="cem-avatar cem-member-avatar">
                                @else
                                    <span class="cem-avatar cem-member-avatar cem-avatar-placeholder">{{ strtoupper(substr($report->user->name, 0, 1)) }}</span>
                                @endif
                                <div>
                                    <div class="small">Par <button type="button" class="btn btn-link p-0 border-0 align-baseline text-decoration-none fw-semibold member-profile-trigger" data-member-name="{{ $report->user->name }}" data-member-role="{{ ucfirst($report->user->role) }}" data-member-position="{{ $report->user->position }}" data-member-department="{{ $report->user->department }}" data-member-domicile="{{ $report->user->domicile }}" data-member-phone="{{ $report->user->formatted_phone }}" data-member-email="{{ $report->user->email }}" data-member-bio="{{ $report->user->bio }}" data-member-avatar="{{ $report->user->avatar_path ? route('profile.avatar', $report->user) : '' }}" data-member-initial="{{ strtoupper(substr($report->user->name, 0, 1)) }}" data-member-message-url="{{ route('private.messages.user', $report->user) }}" data-member-is-current="{{ $report->user->id === auth()->id() ? '1' : '0' }}">{{ $report->user->name }}</button></div>
                                    <div class="cem-user-meta text-capitalize">{{ $report->user->role }}{{ $report->user->position ? ' - '.$report->user->position : '' }}</div>
                                    <div class="small cem-soft">{{ $report->submitted_at?->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap align-items-start">
                            @if($report->attachment_path)
                                <a href="{{ route('reports.download', $report) }}" class="btn btn-outline-secondary btn-sm">Télécharger</a>
                            @endif
                            @if($isDirector || auth()->id() === $report->user_id)
                                <form method="POST" action="{{ route('reports.destroy', $report) }}" onsubmit="return confirm('Supprimer ce rapport ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <p class="mt-3 mb-0">{{ $report->content }}</p>
                </article>
            @empty
                <div class="text-center cem-soft py-5">Aucun rapport ne correspond à votre recherche.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="modal fade" id="report-create-modal" tabindex="-1" aria-labelledby="report-create-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content cem-card">
            <div class="modal-header cem-card-header">
                <h2 class="modal-title h5 mb-0" id="report-create-modal-title">Envoyer un rapport</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="report-title" class="form-label">Titre</label>
                        <input id="report-title" type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="report-content" class="form-label">Contenu du rapport</label>
                        <textarea id="report-content" name="content" rows="8" class="form-control @error('content') is-invalid @enderror" required>{{ old('content') }}</textarea>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="report-attachment" class="form-label">Fichier joint</label>
                        <input id="report-attachment" type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
                        <div class="form-text">PDF, Excel, Word, PowerPoint, image, texte ou ZIP jusqu'à 20 Mo.</div>
                        @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-cem">Envoyer le rapport</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="member-profile-modal" tabindex="-1" aria-labelledby="member-profile-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered cem-profile-modal-dialog">
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
                    <div class="col-6">
                        <div class="cem-info-box">
                            <div class="cem-info-box-header">
                                <span class="cem-info-box-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M9 8h1"/><path d="M9 12h1"/><path d="M9 16h1"/><path d="M14 8h1"/><path d="M14 12h1"/><path d="M14 16h1"/><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/></svg>
                                </span>
                                <span>Département</span>
                            </div>
                            <strong id="member-profile-department"></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="cem-info-box">
                            <div class="cem-info-box-header">
                                <span class="cem-info-box-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                </span>
                                <span>Domicile</span>
                            </div>
                            <strong id="member-profile-domicile"></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="cem-info-box">
                            <div class="cem-info-box-header">
                                <span class="cem-info-box-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </span>
                                <span>Téléphone</span>
                            </div>
                            <strong id="member-profile-phone"></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="cem-info-box">
                            <div class="cem-info-box-header">
                                <span class="cem-info-box-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                </span>
                                <span>Email</span>
                            </div>
                            <strong id="member-profile-email" class="cem-info-box-email"></strong>
                        </div>
                    </div>
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
            phone.textContent = (window.formatMadagascarPhone ? window.formatMadagascarPhone(member.memberPhone) : member.memberPhone) || 'Non renseigné';
            email.textContent = member.memberEmail || 'Non renseigné';
            email.title = member.memberEmail || '';
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

@push('scripts')
@if($errors->has('title') || $errors->has('content') || $errors->has('attachment'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('report-create-modal')).show();
    });
</script>
@endif
@endpush