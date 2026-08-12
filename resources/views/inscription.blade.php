<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription | CEMARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .register-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
    </style>
</head>
<body>

<div class="register-card">
    <h2 class="text-center mb-4">Créer votre Ferme</h2>
    
    <form action="{{ url('/inscription') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-bold">Nom de la Ferme</label>
            <input type="text" name="nom_ferme" class="form-control" placeholder="Ex: Ferme SIDI OUMAR" required>
        </div>

        <div class="hr mb-3"><hr></div>

        <div class="mb-3">
            <label class="form-label fw-bold">Votre Nom</label>
            <input type="text" name="name" class="form-control" placeholder="Prénom et Nom" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Confirmation</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Lancer ma gestion</button>
    </form>
</div>

</body>
</html>