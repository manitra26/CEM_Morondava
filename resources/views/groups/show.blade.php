@extends('layouts.app')

@section('title', $group->name)

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="fw-bold mb-1">{{ $group->name }}</h1>
        <p class="cem-soft mb-0">{{ $group->description }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary">Retour</a>
        <form method="POST" action="{{ route('groups.join', $group) }}">
            @csrf
            <button type="submit" class="btn btn-outline-success">Rejoindre</button>
        </form>
        <form method="POST" action="{{ route('groups.leave', $group) }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Quitter</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card cem-card mb-4">
            <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
                <strong>Messages</strong>
                <span class="badge cem-badge">{{ $group->messages->count() }} message(s)</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('messages.store', $group) }}" class="mb-4">
                    @csrf
                    <label class="form-label">Nouveau message</label>
                    <textarea name="content" rows="3" class="form-control mb-3" placeholder="Écrivez votre message ici..." required>{{ old('content') }}</textarea>
                    <button type="submit" class="btn btn-cem">Publier</button>
                </form>

                @forelse ($messages as $message)
                    <div class="border rounded-4 p-3 mb-3 bg-white">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div>
                                <strong>{{ $message->user->name }}</strong>
                                <div class="small cem-soft">{{ $message->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @if(auth()->id() === $message->user_id || auth()->user()->role === 'directeur')
                                <form method="POST" action="{{ route('messages.destroy', $message) }}" onsubmit="return confirm('Supprimer ce message ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                                </form>
                            @endif
                        </div>
                        <p class="mt-3 mb-0">{{ $message->content }}</p>
                    </div>
                @empty
                    <div class="text-center cem-soft py-4">Aucun message dans ce groupe.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card cem-card mb-4">
            <div class="card-header cem-card-header">
                <strong>Membres</strong>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach ($group->members as $member)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>{{ $member->name }}</span>
                            <span class="badge cem-badge text-capitalize">{{ $member->role }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        @if($isDirector)
            <div class="card cem-card">
                <div class="card-header cem-card-header">
                    <strong>Gérer les membres</strong>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('groups.members.update', $group) }}" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Utilisateur</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Choisir...</option>
                                @foreach($allUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->role }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Action</label>
                            <select name="action" class="form-select" required>
                                <option value="add">Ajouter au groupe</option>
                                <option value="remove">Retirer du groupe</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-cem w-100">Mettre à jour</button>
                    </form>
                    <p class="small cem-soft mb-0">Le directeur ou le créateur du groupe peut ajouter ou retirer des membres.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
