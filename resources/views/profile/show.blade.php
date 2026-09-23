@extends('layouts.app')

@section('title', 'Profil de '.$user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card cem-card overflow-hidden">
            <div class="cem-profile-cover"></div>
            <div class="card-body p-4">
                <div class="d-flex flex-wrap gap-4 align-items-end mb-4">
                    @if($user->avatar_path)
                        <img src="{{ route('profile.avatar', $user) }}" alt="Photo de {{ $user->name }}" class="cem-avatar cem-avatar-lg">
                    @else
                        <div class="cem-avatar cem-avatar-lg cem-avatar-placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    @endif
                    <div class="pb-2">
                        <h1 class="fw-bold mb-1">{{ $user->name }}</h1>
                        <span class="badge cem-badge text-capitalize">{{ $user->role }}</span>
                        @if($user->position)<div class="cem-soft mt-2">{{ $user->position }}</div>@endif
                    </div>
                </div>
                <p class="lead">{{ $user->bio ?: 'Aucune biographie renseignée.' }}</p>
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <div class="cem-info-box">
                            <div class="cem-info-box-header">
                                <span class="cem-info-box-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M9 8h1"/><path d="M9 12h1"/><path d="M9 16h1"/><path d="M14 8h1"/><path d="M14 12h1"/><path d="M14 16h1"/><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/></svg>
                                </span>
                                <span>Département</span>
                            </div>
                            <strong>{{ $user->department ?: 'Non renseigné' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cem-info-box">
                            <div class="cem-info-box-header">
                                <span class="cem-info-box-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                </span>
                                <span>Domicile</span>
                            </div>
                            <strong>{{ $user->domicile ?: 'Non renseigné' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cem-info-box">
                            <div class="cem-info-box-header">
                                <span class="cem-info-box-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </span>
                                <span>Téléphone</span>
                            </div>
                            <strong>{{ $user->formatted_phone ?: 'Non renseigné' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cem-info-box">
                            <div class="cem-info-box-header">
                                <span class="cem-info-box-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                </span>
                                <span>Email</span>
                            </div>
                            <strong class="cem-info-box-email" title="{{ $user->email }}">{{ $user->email }}</strong>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    @if(auth()->id() !== $user->id)<a href="{{ route('private.messages.user', $user) }}" class="btn btn-cem">Message</a>@endif
                    @if(auth()->id() === $user->id)<a href="{{ route('profile.edit') }}" class="btn btn-cem">Modifier mon profil</a>@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
