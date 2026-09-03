<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - CEM Morondava</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: radial-gradient(circle at top, #14424a 0, #0d1f24 48%, #071114 100%);
            color: white;
        }
        .register-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 2rem;
        }
        .register-card {
            width: 100%;
            max-width: 860px;
            overflow: hidden;
            border-radius: 1.75rem;
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(18px);
            box-shadow: 0 30px 80px rgba(0,0,0,.32);
            border: 1px solid rgba(255,255,255,.12);
        }
        .register-visual {
            background: linear-gradient(160deg, #d87c4d 0%, #1c7c6c 100%);
            padding: 3rem;
            min-height: 100%;
        }
        .register-panel {
            background: #f8faf9;
            color: #14343c;
            padding: 3rem;
        }
        .soft {
            color: rgba(20, 52, 60, .72);
        }
    </style>
</head>
<body>
<div class="register-shell">
    <div class="register-card row g-0">
        <div class="col-lg-5 register-visual d-flex flex-column justify-content-between">
            <div>
                <span class="badge rounded-pill text-bg-light mb-4">Nouveau compte CEM</span>
                <h1 class="display-6 fw-bold">Créer un accès interne</h1>
                <p class="lead mt-4 mb-0">L'inscription crée un compte employé pour rejoindre la communication interne du centre.</p>
            </div>
            <div class="mt-5">
                <p class="mt-4 mb-0 small opacity-75">Le rôle <strong>directeur</strong> est réservé à l'administration interne.</p>
            </div>
        </div>
        <div class="col-lg-7 register-panel">
            <h2 class="fw-bold mb-2">Inscription</h2>
            <p class="soft mb-4">Complétez vos informations pour créer votre compte employé.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="mt-3">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nom complet</label>
                    <input type="text" name="name" class="form-control form-control-lg" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Adresse email</label>
                    <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" required>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-control form-control-lg" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirmation</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-lg" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-cem btn-lg w-100 mt-4">Créer mon compte</button>
                <p class="mt-3 mb-0 text-center">
                    Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a>
                </p>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
