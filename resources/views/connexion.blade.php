<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- AJOUT DE LA BALISE CSRF META -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion | CEMARA Multi-Ferme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background:  url("{{ asset('images/poules.png') }}") center/cover no-repeat fixed;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-card {
            background: #ffffff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-card h2 {
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-card p {
            color: #636e72;
            text-align: center;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #dfe6e9;
            background-color: #f1f2f6;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #0984e3;
            background-color: #fff;
        }

        .btn-login {
            background: #18828a;
            color: #fff;
            font-weight: 600;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
            border: none;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #18828a;
            transform: translateY(-2px);
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            font-size: 0.8rem;
            color: #b2bec3;
        }

        .alert-custom {
            border-radius: 10px;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <div style="background: #18828a; width: 60px; height: 60px; border-radius: 15px; display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 30px; font-weight: bold;">
                C
            </div>
        </div>

        <h2>Bienvenue</h2>
        <p>Connectez-vous pour gérer votre ferme</p>

        @if ($errors->any())
            <div class="alert alert-danger alert-custom">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- ACTION MODIFIÉE ICI -->
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label small fw-bold">Email</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="nom@exemple.com"
                    required value="{{ old('email') }}">
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-bold">Mot de passe</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="••••••••"
                    required>
            </div>

            <button type="submit" class="btn btn-login">Se connecter</button>
        </form>

        <div class="footer-text">
            &copy; 2026 CEMARA Edu. Tous droits réservés.
        </div>
    </div>

</body>

</html>
