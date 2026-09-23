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
                        <div class="col-md-6">
                            <label class="form-label" for="profile-name">Nom complet</label>
                            <div class="cem-input-icon-wrapper">
                                <span class="cem-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </span>
                                <input id="profile-name" name="name" class="form-control cem-input-with-icon" value="{{ old('name', $user->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="profile-email">Email</label>
                            <div class="cem-input-icon-wrapper">
                                <span class="cem-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                                        <path d="m3 7 9 6 9-6"/>
                                    </svg>
                                </span>
                                <input id="profile-email" type="email" name="email" class="form-control cem-input-with-icon" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="profile-position">Poste de travail</label>
                            <div class="cem-input-icon-wrapper">
                                <span class="cem-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                    </svg>
                                </span>
                                <input id="profile-position" name="position" class="form-control cem-input-with-icon" value="{{ old('position', $user->position) }}" placeholder="Responsable des guides">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="profile-department">D&eacute;partement</label>
                            <div class="cem-input-icon-wrapper">
                                <span class="cem-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="4" y="2" width="16" height="20" rx="2"/>
                                        <path d="M9 22v-4h6v4M8 6h.01M16 6h.01M8 10h.01M16 10h.01M8 14h.01M16 14h.01"/>
                                    </svg>
                                </span>
                                <input id="profile-department" name="department" class="form-control cem-input-with-icon" value="{{ old('department', $user->department) }}" placeholder="Accueil, Exploitation...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="profile-domicile">Domicile</label>
                            <div class="cem-input-icon-wrapper">
                                <span class="cem-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                </span>
                                <input id="profile-domicile" name="domicile" class="form-control cem-input-with-icon" value="{{ old('domicile', $user->domicile) }}" placeholder="Morondava">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="profile-phone">Num&eacute;ro de t&eacute;l&eacute;phone</label>
                            <div class="cem-input-icon-wrapper">
                                <span class="cem-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                </span>
                                <input id="profile-phone" type="tel" name="phone" class="form-control cem-input-with-icon" value="{{ old('phone', $user->phone) }}" placeholder="032 67 432 72" maxlength="13" inputmode="numeric" data-phone-input>
                            </div>
                            <div class="form-text small cem-soft">Format malgache &agrave; 10 chiffres (ex: 032 67 432 72).</div>
                        </div>
                        <div class="col-12"><label class="form-label" for="profile-bio">Biographie</label><textarea id="profile-bio" name="bio" rows="4" class="form-control" maxlength="2000" placeholder="Quelques mots sur vos activit&eacute;s...">{{ old('bio', $user->bio) }}</textarea></div>
                        <div class="col-md-7">
                            <label class="form-label" for="profile-avatar">Photo de profil</label>
                            <div class="cem-input-icon-wrapper">
                                <span class="cem-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <path d="m21 15-5-5L5 21"/>
                                    </svg>
                                </span>
                                <input id="profile-avatar" type="file" name="avatar" class="form-control cem-input-with-icon" accept=".jpg,.jpeg,.png,.webp">
                            </div>
                            <div class="form-text small cem-soft">JPG, PNG ou WEBP, 5 Mo maximum.</div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="profile-theme">Th&egrave;me de l'application</label>
                            <div class="cem-input-icon-wrapper">
                                <span class="cem-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="4"/>
                                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                                    </svg>
                                </span>
                                <select id="profile-theme" name="theme" class="form-select cem-input-with-icon">
                                    <option value="system" @selected($user->theme === 'system')>Syst&egrave;me</option>
                                    <option value="light" @selected($user->theme === 'light')>Clair</option>
                                    <option value="dark" @selected($user->theme === 'dark')>Sombre</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4"><button class="btn btn-cem">Enregistrer</button><a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Annuler</a></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
