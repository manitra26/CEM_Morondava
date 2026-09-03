@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card cem-card">
            <div class="card-header cem-card-header">
                <strong>Créer un utilisateur</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmation</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rôle</label>
                        <select name="role" class="form-select" required>
                            <option value="employe">Employé</option>
                            <option value="directeur">Directeur</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Poste</label>
                        <input type="text" name="position" class="form-control" value="{{ old('position') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Département</label>
                        <input type="text" name="department" class="form-control" value="{{ old('department') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" class="form-control" rows="4">{{ old('bio') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-cem w-100">Créer</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card cem-card">
            <div class="card-header cem-card-header d-flex justify-content-between align-items-center">
                <strong>Utilisateurs</strong>
                <span class="badge cem-badge">{{ $users->count() }} compte(s)</span>
            </div>
            <div class="card-body">
                @foreach($users as $user)
                    <div class="border rounded-4 p-3 mb-3 bg-white">
                        <form method="POST" action="{{ route('users.update', $user) }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom</label>
                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Rôle</label>
                                    <select name="role" class="form-select" required>
                                        <option value="employe" @selected($user->role === 'employe')>Employé</option>
                                        <option value="directeur" @selected($user->role === 'directeur')>Directeur</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Poste</label>
                                    <input type="text" name="position" class="form-control" value="{{ $user->position }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Département</label>
                                    <input type="text" name="department" class="form-control" value="{{ $user->department }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Téléphone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Bio</label>
                                    <input type="text" name="bio" class="form-control" value="{{ $user->bio }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nouveau mot de passe</label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Confirmation</label>
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                <div class="small cem-soft">
                                    {{ $user->isDirector() ? 'Directeur' : 'Employé' }}
                                </div>
                                <button type="submit" class="btn btn-cem btn-sm">Enregistrer</button>
                            </div>
                        </form>
                        @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="mt-2" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
