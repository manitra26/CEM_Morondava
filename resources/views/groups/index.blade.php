@extends('layouts.app')

@section('title', 'Discussions de groupe')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card cem-card mb-4">
            <div class="card-header cem-card-header">
                <strong>Créer un groupe</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('groups.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nom du groupe</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="6" class="form-control" required>{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-cem w-100">Créer le groupe</button>
                </form>
            </div>
        </div>

        @if($isDirector)
            <div class="card cem-card">
                <div class="card-header cem-card-header">
                    <strong>Membres disponibles</strong>
                </div>
                <div class="card-body">
                    <div class="small cem-soft">Utilisateurs enregistrés dans le système:</div>
                    <ul class="list-group list-group-flush mt-3">
                        @foreach($allUsers as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $user->name }}</span>
                                <span class="badge cem-badge text-capitalize">{{ $user->role }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-8">
        <div class="card cem-card">
            <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
                <strong>Groupes de discussion</strong>
                <span class="badge cem-badge">{{ $groups->count() }} groupe(s)</span>
            </div>
            <div class="card-body">
                @forelse ($groups as $group)
                    <div class="border rounded-4 p-3 mb-3 bg-white">
                        <div class="d-flex justify-content-between flex-wrap gap-3">
                            <div>
                                <h5 class="mb-1">{{ $group->name }}</h5>
                                <div class="small cem-soft">Créé par {{ $group->creator->name }} - {{ $group->members->count() }} membre(s)</div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('groups.show', $group) }}" class="btn btn-cem btn-sm">Ouvrir</a>
                                <form method="POST" action="{{ route('groups.join', $group) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success btn-sm">Rejoindre</button>
                                </form>
                                <form method="POST" action="{{ route('groups.leave', $group) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">Quitter</button>
                                </form>
                            </div>
                        </div>
                        <p class="mt-3 mb-0">{{ $group->description }}</p>
                    </div>
                @empty
                    <div class="text-center cem-soft py-4">Aucun groupe pour le moment.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
