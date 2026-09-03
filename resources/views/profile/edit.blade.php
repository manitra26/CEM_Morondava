@extends('layouts.app')

@section('title', 'Profil et paramètres')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="card cem-card">
            <div class="card-header cem-card-header"><strong>Profil et paramètres</strong></div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nom complet</label><input name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
                        <div class="col-md-6"><label class="form-label">Poste de travail</label><input name="position" class="form-control" value="{{ old('position', $user->position) }}" placeholder="Responsable des guides"></div>
                        <div class="col-md-6"><label class="form-label">Département</label><input name="department" class="form-control" value="{{ old('department', $user->department) }}"></div>
                        <div class="col-md-6"><label class="form-label">Domicile</label><input name="domicile" class="form-control" value="{{ old('domicile', $user->domicile) }}" placeholder="Morondava"></div>
                        <div class="col-md-6"><label class="form-label">Numéro de téléphone</label><input name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"></div>
                        <div class="col-12"><label class="form-label">Biographie</label><textarea name="bio" rows="4" class="form-control" maxlength="2000">{{ old('bio', $user->bio) }}</textarea></div>
                        <div class="col-md-7"><label class="form-label">Photo de profil</label><input type="file" name="avatar" class="form-control" accept=".jpg,.jpeg,.png,.webp"><div class="form-text">JPG, PNG ou WEBP, 5 Mo maximum.</div></div>
                        <div class="col-md-5"><label class="form-label">Thème de l'application</label><select name="theme" class="form-select"><option value="system" @selected($user->theme === 'system')>Système</option><option value="light" @selected($user->theme === 'light')>Clair</option><option value="dark" @selected($user->theme === 'dark')>Sombre</option></select></div>
                    </div>
                    <div class="d-flex gap-2 mt-4"><button class="btn btn-cem">Enregistrer</button><a href="{{ route('profile.show', $user) }}" class="btn btn-outline-secondary">Annuler</a></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
