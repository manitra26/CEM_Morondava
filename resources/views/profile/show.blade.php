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
                    <div class="col-md-6"><div class="cem-info-box"><span>Département</span><strong>{{ $user->department ?: 'Non renseigné' }}</strong></div></div>
                    <div class="col-md-6"><div class="cem-info-box"><span>Domicile</span><strong>{{ $user->domicile ?: 'Non renseigné' }}</strong></div></div>
                    <div class="col-md-6"><div class="cem-info-box"><span>Téléphone</span><strong>{{ $user->phone ?: 'Non renseigné' }}</strong></div></div>
                    <div class="col-md-6"><div class="cem-info-box"><span>Email</span><strong>{{ $user->email }}</strong></div></div>
                </div>
                @if(auth()->id() === $user->id)<a href="{{ route('profile.edit') }}" class="btn btn-cem mt-4">Modifier mon profil</a>@endif
            </div>
        </div>
    </div>
</div>
@endsection
