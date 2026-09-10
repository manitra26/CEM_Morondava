@extends('layouts.app')

@section('title', 'Discussions de groupe')

@section('content')
<div class="groups-page">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Discussions de groupe</h1>
            <p class="cem-soft mb-0">Retrouvez vos groupes et échangez facilement avec votre équipe.</p>
        </div>
        @if($isDirector)
            <button type="button" class="btn btn-cem px-4" data-bs-toggle="modal" data-bs-target="#group-create-modal">
                + Créer un groupe
            </button>
        @endif
    </div>

    <div class="card cem-card groups-list-card">
        <div class="card-header cem-card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <strong>Groupes de discussion</strong>
                <span class="badge cem-badge">{{ $groups->count() }} groupe(s)</span>
            </div>
            <form method="GET" action="{{ route('groups.index') }}" class="row g-2 align-items-end">
                <div class="col-md-9">
                    <label for="group-search" class="form-label small mb-1">Rechercher un groupe</label>
                    <input id="group-search" type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Nom ou description du groupe">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-cem flex-grow-1">Rechercher</button>
                    <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary">Effacer</a>
                </div>
            </form>
        </div>
        <div class="card-body groups-list-scroll">
            @forelse ($groups as $group)
                <article class="border rounded-4 p-3 mb-3 bg-white group-list-item">
                    <div class="d-flex justify-content-between flex-wrap gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-3">
                                @if($group->image_path)
                                    <img src="{{ route('groups.image', $group) }}" alt="Image du groupe" class="cem-avatar cem-group-avatar">
                                @else
                                    <span class="cem-avatar cem-group-avatar cem-avatar-placeholder">{{ strtoupper(substr($group->name, 0, 1)) }}</span>
                                @endif
                                <h5 class="mb-1">{{ $group->name }}</h5>
                            </div>
                            <div class="small cem-soft">Créé par {{ $group->creator->name }} - {{ $group->members->count() }} membre(s)</div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap align-items-start">
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
                </article>
            @empty
                <div class="text-center cem-soft py-5">Aucun groupe ne correspond à votre recherche.</div>
            @endforelse
        </div>
    </div>

    @if($isDirector)
        <div class="card cem-card groups-members-card mt-4">
            <div class="card-header cem-card-header">
                <strong>Membres disponibles</strong>
            </div>
            <div class="card-body groups-members-scroll">
                <div class="small cem-soft">Utilisateurs enregistrés dans le système :</div>
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

@if($isDirector)
    <div class="modal fade" id="group-create-modal" tabindex="-1" aria-labelledby="group-create-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content cem-card">
                <div class="modal-header cem-card-header">
                    <h2 class="modal-title h5 mb-0" id="group-create-modal-title">Créer un groupe</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form method="POST" action="{{ route('groups.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="group-name" class="form-label">Nom du groupe</label>
                            <input id="group-name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="group-description" class="form-label">Description</label>
                            <textarea id="group-description" name="description" rows="6" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="group-image" class="form-label">Image du groupe</label>
                            <input id="group-image" type="file" name="group_image" class="form-control @error('group_image') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp">
                            <div class="form-text">JPG, PNG ou WEBP, 5 Mo maximum.</div>
                            @error('group_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-cem">Créer le groupe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
@if($errors->has('name') || $errors->has('description') || $errors->has('group_image'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('group-create-modal')).show();
    });
</script>
@endif
@endpush