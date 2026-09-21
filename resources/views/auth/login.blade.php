<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - CEM Morondava</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: radial-gradient(circle at top, #14424a 0, #0d1f24 48%, #071114 100%);
            color: white;
        }
        .login-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 2rem;
        }
        .login-card {
            width: 100%;
            max-width: 980px;
            overflow: hidden;
            border-radius: 1.75rem;
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(18px);
            box-shadow: 0 30px 80px rgba(0,0,0,.32);
            border: 1px solid rgba(255,255,255,.12);
        }
        .login-visual {
            background: linear-gradient(160deg, #1c7c6c 0%, #d87c4d 100%);
            padding: 3rem;
            min-height: 100%;
        }
        .login-panel {
            background: #f8faf9;
            color: #14343c;
            padding: 3rem;
        }
        .soft {
            color: rgba(20, 52, 60, .72);
        }
        .password-field {
            position: relative;
        }
        .password-field .form-control {
            padding-right: 3rem;
        }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 0.85rem;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #5d6d73;
            display: grid;
            place-items: center;
            width: 2rem;
            height: 2rem;
            padding: 0;
            cursor: pointer;
        }
        .password-toggle:focus {
            outline: 2px solid rgba(28, 124, 108, 0.4);
            border-radius: 50%;
        }
        .password-toggle svg {
            width: 1.2rem;
            height: 1.2rem;
        }
    </style>
</head>
<body>
<div class="login-shell">
    <div class="login-card row g-0">
        <div class="col-lg-5 login-visual d-flex flex-column justify-content-between">
            <div>
                <span class="badge rounded-pill text-bg-light mb-4">Plateforme de communication interne</span>
                <h1 class="display-6 fw-bold">CEM Morondava</h1>
                <p class="lead mt-4 mb-0">Centralisez les rapports journaliers, organisez les discussions de groupe et gardez une traçabilité claire des informations internes.</p>
            </div>
            <div class="mt-5">
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge text-bg-light text-dark">Rapports</span>
                    <span class="badge text-bg-light text-dark">Discussion de groupe</span>
                    <span class="badge text-bg-light text-dark">Notifications</span>
                </div>
                <p class="mt-4 mb-0 small opacity-75">Comptes de démonstration: <strong>m.randria@cem-morondava.mg</strong> ou <strong>j.rakoto@cem-morondava.mg</strong> / mot de passe: <strong>password</strong></p>
            </div>
        </div>
        <div class="col-lg-7 login-panel">
            <h2 class="fw-bold mb-2">S'authentifier</h2>
            <p class="soft mb-4">Connectez-vous pour accéder au tableau de bord du CEM.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="mt-3">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Adresse email</label>
                    <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <div class="password-field">
                        <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required>
                        <button type="button" class="password-toggle" data-password-toggle aria-label="Afficher le mot de passe" title="Afficher le mot de passe">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Rester connecté</label>
                </div>
                <button type="submit" class="btn btn-cem btn-lg w-100">Se connecter</button>
                <p class="mt-3 mb-0 text-center">
                    Nouveau compte ? <a href="{{ route('register') }}">S'inscrire</a>
                </p>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        const field = button.closest('.password-field');
        const input = field.querySelector('input');
        const eyeOpen = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        `;
        const eyeClosed = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 3l18 18"></path>
                <path d="M10.58 10.58A2 2 0 0 0 13.42 13.42"></path>
                <path d="M9.88 5.08A10.94 10.94 0 0 1 12 5c6.5 0 10 7 10 7a16.77 16.77 0 0 1-4.21 5.32"></path>
                <path d="M6.61 6.61A16.8 16.8 0 0 0 2 12s3.5 7 10 7a11.17 11.17 0 0 0 5.39-1.61"></path>
            </svg>
        `;

        button.addEventListener('click', function () {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.innerHTML = isHidden ? eyeClosed : eyeOpen;
            button.setAttribute('aria-label', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
            button.setAttribute('title', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        });
    });
</script>
</body>
</html>
