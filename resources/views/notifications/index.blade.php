@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="card cem-card">
    <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
        <strong>Notifications internes</strong>
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-cem">Tout marquer comme lu</button>
        </form>
    </div>
    <div class="card-body">
        @forelse ($notifications as $notification)
            <div class="border rounded-4 p-3 mb-3 bg-white d-flex justify-content-between gap-3 flex-wrap">
                <div>
                    @if($notification->actor)
                        <div class="d-flex align-items-center gap-2 mb-3">
                            @if($notification->actor->avatar_path)
                                <img src="{{ route('profile.avatar', $notification->actor) }}" alt="Photo de {{ $notification->actor->name }}" class="cem-avatar cem-member-avatar">
                            @else
                                <span class="cem-avatar cem-member-avatar cem-avatar-placeholder">{{ strtoupper(substr($notification->actor->name, 0, 1)) }}</span>
                            @endif
                            <div><button type="button" class="btn btn-link p-0 border-0 text-decoration-none fw-semibold member-profile-trigger" data-member-name="{{ $notification->actor->name }}" data-member-role="{{ ucfirst($notification->actor->role) }}" data-member-position="{{ $notification->actor->position }}" data-member-department="{{ $notification->actor->department }}" data-member-domicile="{{ $notification->actor->domicile }}" data-member-phone="{{ $notification->actor->formatted_phone }}" data-member-email="{{ $notification->actor->email }}" data-member-bio="{{ $notification->actor->bio }}" data-member-avatar="{{ $notification->actor->avatar_path ? route('profile.avatar', $notification->actor) : '' }}" data-member-initial="{{ strtoupper(substr($notification->actor->name, 0, 1)) }}" data-member-message-url="{{ route('private.messages.user', $notification->actor) }}" data-member-is-current="{{ $notification->actor->id === auth()->id() ? '1' : '0' }}">{{ $notification->actor->name }}</button><div class="cem-user-meta text-capitalize">{{ $notification->actor->role }}{{ $notification->actor->position ? ' - '.$notification->actor->position : '' }}</div></div>
                        </div>
                    @endif
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h5 class="mb-0">{{ $notification->title }}</h5>
                        @if(! $notification->is_read)
                            <span class="badge cem-badge">Non lue</span>
                        @else
                            <span class="badge text-bg-secondary">Lue</span>
                        @endif
                    </div>
                    <div class="small cem-soft mt-1">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                    <p class="mt-2 mb-0">{{ $notification->content }}</p>
                </div>
                @if(! $notification->is_read)
                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-success btn-sm">Marquer lue</button>
                    </form>
                @endif
            </div>
        @empty
            <div class="text-center cem-soft py-4">Aucune notification pour le moment.</div>
        @endforelse
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
@endsection

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