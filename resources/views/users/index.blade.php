@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="card cem-card">
    <div class="card-header cem-card-header d-flex justify-content-between align-items-center gap-3 flex-wrap">
        <strong>Utilisateurs</strong>
        <div class="d-flex align-items-center gap-2">
            <span class="badge cem-badge">{{ $users->count() }} compte(s)</span>
            <button type="button" class="btn btn-cem" data-bs-toggle="modal" data-bs-target="#create-user-modal">+ Cr&eacute;er un utilisateur</button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('users.index') }}" class="row g-2 mb-4">
            <div class="col-md">
                <label for="user-search" class="visually-hidden">Rechercher un utilisateur</label>
                <input id="user-search" type="search" name="search" class="form-control" value="{{ $search }}" placeholder="Rechercher par nom, e-mail ou t&eacute;l&eacute;phone">
            </div>
            <div class="col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-cem">Filtrer</button>
                @if ($search !== '')
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">R&eacute;initialiser</a>
                @endif
            </div>
        </form>

        @forelse($users as $user)
            <div class="border rounded-4 p-3 mb-3 bg-white">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="name-{{ $user->id }}">Nom</label>
                            <input id="name-{{ $user->id }}" type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email-{{ $user->id }}">Email</label>
                            <input id="email-{{ $user->id }}" type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="role-{{ $user->id }}">R&ocirc;le</label>
                            <select id="role-{{ $user->id }}" name="role" class="form-select" required>
                                <option value="employe" @selected($user->role === 'employe')>Employ&eacute;</option>
                                <option value="directeur" @selected($user->role === 'directeur')>Directeur</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="position-{{ $user->id }}">Poste</label>
                            <input id="position-{{ $user->id }}" type="text" name="position" class="form-control" value="{{ $user->position }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="department-{{ $user->id }}">D&eacute;partement</label>
                            <input id="department-{{ $user->id }}" type="text" name="department" class="form-control" value="{{ $user->department }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="phone-{{ $user->id }}">T&eacute;l&eacute;phone</label>
                            <input id="phone-{{ $user->id }}" type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="bio-{{ $user->id }}">Bio</label>
                            <input id="bio-{{ $user->id }}" type="text" name="bio" class="form-control" value="{{ $user->bio }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="password-{{ $user->id }}">Nouveau mot de passe</label>
                            <div class="input-group">
                                <input id="password-{{ $user->id }}" type="password" name="password" class="form-control" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary" data-password-toggle data-password-target="password-{{ $user->id }}">Afficher</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="password-confirmation-{{ $user->id }}">Confirmation</label>
                            <div class="input-group">
                                <input id="password-confirmation-{{ $user->id }}" type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary" data-password-toggle data-password-target="password-confirmation-{{ $user->id }}">Afficher</button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <div class="small cem-soft">{{ $user->isDirector() ? 'Directeur' : 'Employ&eacute;' }}</div>
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
        @empty
            <div class="text-center cem-soft py-4">Aucun utilisateur ne correspond &agrave; votre recherche.</div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="create-user-modal" tabindex="-1" aria-labelledby="create-user-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content cem-card">
            <div class="modal-header cem-card-header">
                <h2 id="create-user-modal-title" class="modal-title h5 mb-0">Cr&eacute;er un utilisateur</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <input type="hidden" name="create_user_form" value="1">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="create-name">Nom</label><input id="create-name" type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
                        <div class="col-md-6"><label class="form-label" for="create-email">Email</label><input id="create-email" type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
                        <div class="col-md-6"><label class="form-label" for="create-password">Mot de passe</label><div class="input-group"><input id="create-password" type="password" name="password" class="form-control" autocomplete="new-password" required><button type="button" class="btn btn-outline-secondary" data-password-toggle data-password-target="create-password">Afficher</button></div></div>
                        <div class="col-md-6"><label class="form-label" for="create-password-confirmation">Confirmation</label><div class="input-group"><input id="create-password-confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required><button type="button" class="btn btn-outline-secondary" data-password-toggle data-password-target="create-password-confirmation">Afficher</button></div></div>
                        <div class="col-md-4"><label class="form-label" for="create-role">R&ocirc;le</label><select id="create-role" name="role" class="form-select" required><option value="employe" @selected(old('role', 'employe') === 'employe')>Employ&eacute;</option><option value="directeur" @selected(old('role') === 'directeur')>Directeur</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="create-position">Poste</label><input id="create-position" type="text" name="position" class="form-control" value="{{ old('position') }}"></div>
                        <div class="col-md-4"><label class="form-label" for="create-department">D&eacute;partement</label><input id="create-department" type="text" name="department" class="form-control" value="{{ old('department') }}"></div>
                        <div class="col-md-6"><label class="form-label" for="create-phone">T&eacute;l&eacute;phone</label><input id="create-phone" type="text" name="phone" class="form-control" value="{{ old('phone') }}"></div>
                        <div class="col-md-6"><label class="form-label" for="create-bio">Bio</label><input id="create-bio" type="text" name="bio" class="form-control" value="{{ old('bio') }}"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-cem">Cr&eacute;er l'utilisateur</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordTarget);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.textContent = isPassword ? 'Masquer' : 'Afficher';
        });
    });

    @if (old('create_user_form'))
        bootstrap.Modal.getOrCreateInstance(document.getElementById('create-user-modal')).show();
    @endif
})();
</script>
@endpush