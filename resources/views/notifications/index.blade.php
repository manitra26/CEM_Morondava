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
                            <div><button type="button" class="btn btn-link p-0 border-0 text-decoration-none fw-semibold member-profile-trigger" data-member-name="{{ $notification->actor->name }}" data-member-role="{{ ucfirst($notification->actor->role) }}" data-member-position="{{ $notification->actor->position }}" data-member-department="{{ $notification->actor->department }}" data-member-domicile="{{ $notification->actor->domicile }}" data-member-phone="{{ $notification->actor->phone }}" data-member-email="{{ $notification->actor->email }}" data-member-bio="{{ $notification->actor->bio }}" data-member-avatar="{{ $notification->actor->avatar_path ? route('profile.avatar', $notification->actor) : '' }}" data-member-initial="{{ strtoupper(substr($notification->actor->name, 0, 1)) }}" data-member-message-url="{{ route('private.messages.user', $notification->actor) }}" data-member-is-current="{{ $notification->actor->id === auth()->id() ? '1' : '0' }}">{{ $notification->actor->name }}</button><div class="cem-user-meta text-capitalize">{{ $notification->actor->role }}{{ $notification->actor->position ? ' - '.$notification->actor->position : '' }}</div></div>
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
                    <div class="col-6"><div class="cem-info-box"><span>D&eacute;partement</span><strong id="member-profile-department"></strong></div></div>
                    <div class="col-6"><div class="cem-info-box"><span>Domicile</span><strong id="member-profile-domicile"></strong></div></div>
                    <div class="col-6"><div class="cem-info-box"><span>T&eacute;l&eacute;phone</span><strong id="member-profile-phone"></strong></div></div>
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
            position.textContent = member.memberPosition || 'Poste non renseign\u00e9';
            bio.textContent = member.memberBio || 'Aucune biographie renseign\u00e9e.';
            department.textContent = member.memberDepartment || 'Non renseign\u00e9';
            domicile.textContent = member.memberDomicile || 'Non renseign\u00e9';
            phone.textContent = member.memberPhone || 'Non renseign\u00e9';
            email.textContent = member.memberEmail || 'Non renseign\u00e9';
            initial.textContent = member.memberInitial;
