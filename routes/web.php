<?php
use App\Http\Controllers\AchatController;
use App\Http\Controllers\AlimentationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\FermeController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LotSuiviController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\ModeController;
use App\Http\Controllers\PerteController;
use App\Http\Controllers\PoulaillerController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\TransformationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VaccinationController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\VeterinaireController;
use Illuminate\Support\Facades\Route;


Route::get('/test-cookie-raw', function () {
    // 1. Force l'envoi d'un cookie HTTP pur via les en-têtes PHP
    header('Set-Cookie: test_manuel=SUCCES; Path=/; Secure; HttpOnly; SameSite=Lax');
    
    // 2. Initialise la session PHP native
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['test_native'] = 'OK';

    return 'Test cookie manuel envoyé ! Session native : ' . $_SESSION['test_native'];
});

// --- TEST DE SESSION SANS AUTHENTIFICATION (À supprimer en production) ---
Route::get('/test-session-set', function () {
    session(['mon_test' => 'SESSION_OK']);
    return redirect('/test-session-get');
});

Route::get('/test-session-get', function () {
    return 'Résultat de la session : ' . session('mon_test', 'PERDUE !');
});

// --- ROUTES PUBLIQUES ---
Route::get('/', [AuthController::class, 'showLogin']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
Route::post('/inscription', [AuthController::class, 'register'])->name('register.post');

// --- ROUTES PROTÉGÉES (Connecté) ---
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // --- SUPER ADMIN ---
    Route::get('/SuperAdmin/choix', [FermeController::class, 'index'])->name('SuperAdmin.index');
    Route::post('/SuperAdmin/choisir', [FermeController::class, 'choisir'])->name('superadmin.choisir');

    // --- GESTION DES LOTS ET SUIVI DE CROISSANCE ---
    Route::prefix('Lots')->group(function () {
        Route::get('/', [LotSuiviController::class, 'index'])->name('lots.index');
        Route::post('/store', [LotSuiviController::class, 'storeLot'])->name('lots.storeLot');
        Route::put('/update/{id}', [LotSuiviController::class, 'updateLot'])->name('lots.updateLot');
        Route::delete('/delete/{id}', [LotSuiviController::class, 'deleteLot'])->name('lots.deleteLot');
        Route::post('/cloturer/{id}', [LotSuiviController::class, 'cloturerLot'])->name('lots.cloturer');

        // Suivi
        Route::post('/suivi/store', [LotSuiviController::class, 'storeSuivi'])->name('lots.storeSuivi');
        Route::put('/suivi/update/{id}', [LotSuiviController::class, 'updateSuivi'])->name('lots.updateSuivi');
        Route::delete('/suivi/delete/{id}', [LotSuiviController::class, 'deleteSuivi'])->name('lots.deleteSuivi');
    });

    // --- CLIENTS ---
    Route::get('Clients', [ClientController::class, 'clients']);
    Route::match(['get', 'post'], 'Clients/add-edit/{id?}', [ClientController::class, 'create']);
    Route::get('Clients/delete/{id}', [ClientController::class, 'suppression']);

    // --- FOURNISSEURS ---
    Route::get('Fournisseurs', [FournisseurController::class, 'fournisseurs']);
    Route::match(['get', 'post'], 'Fournisseurs/add-edit/{id?}', [FournisseurController::class, 'create']);
    Route::get('Fournisseurs/delete/{id}', [FournisseurController::class, 'suppression']);

    // --- VÉTÉRINAIRES ---
    Route::get('Veterinaires', [VeterinaireController::class, 'veterinaires']);
    Route::match(['get', 'post'], 'Veterinaires/add-edit/{id?}', [VeterinaireController::class, 'create']);
    Route::get('Veterinaires/delete/{id}', [VeterinaireController::class, 'suppression']);

    // --- PRODUITS ---
    Route::get('Produits', [ProduitController::class, 'produits']);
    Route::match(['get', 'post'], 'Produits/add-edit/{id?}', [ProduitController::class, 'create']);
    Route::get('Produits/delete/{id}', [ProduitController::class, 'suppression']);

    // --- POULAILLERS ---
    Route::get('Poulaillers', [PoulaillerController::class, 'poulaillers']);
    Route::match(['get', 'post'], 'Poulaillers/add-edit/{id?}', [PoulaillerController::class, 'create']);
    Route::get('Poulaillers/delete/{id}', [PoulaillerController::class, 'suppression']);

    // --- MATIÈRES ---
    Route::get('Matieres', [MatiereController::class, 'matieres']);
    Route::match(['get', 'post'], 'Matieres/add-edit/{id?}', [MatiereController::class, 'create']);
    Route::get('Matieres/delete/{id}', [MatiereController::class, 'suppression']);

    // --- MODES DE PAIEMENT ---
    Route::get('Modes', [ModeController::class, 'modes']);
    Route::match(['get', 'post'], 'Modes/add-edit/{id?}', [ModeController::class, 'create']);
    Route::get('Modes/delete/{id}', [ModeController::class, 'suppression']);

    // --- PRODUCTIONS ---
    Route::get('Productions', [ProductionController::class, 'index'])->name('production.index');
    Route::get('/production/create/{id?}', [ProductionController::class, 'create'])->name('production.create');
    Route::post('/production/action', [ProductionController::class, 'storeAction'])->name('production.action');

    // --- VENTES ---
    Route::prefix('Ventes')->group(function () {
        Route::get('/', [VenteController::class, 'index'])->name('ventes.index');
        Route::match(['get', 'post'], '/add-edit/{id?}', [VenteController::class, 'createOrUpdate'])->name('ventes.create');
        Route::get('/delete/{id}', [VenteController::class, 'delete'])->name('ventes.delete');

        // Détails Vente
        Route::post('/acheter/store', [VenteController::class, 'storeProduit'])->name('vendre.store');
        Route::get('/acheter/delete/{id}', [VenteController::class, 'deleteProduit'])->name('vendre.delete');
        Route::get('/acheter/edit/{id}', [VenteController::class, 'editProduit'])->name('vendre.edit');
        Route::post('/acheter/update/{id}', [VenteController::class, 'updateProduit'])->name('vendre.update');

        // Paiements
        Route::post('/paiement/store', [VenteController::class, 'storePaiement'])->name('paiement.store');
        Route::get('/paiement/edit/{id}', [VenteController::class, 'editPaiement'])->name('paiement.edit');
        Route::post('/paiement/update/{id}', [VenteController::class, 'updatePaiement'])->name('paiement.update');
        Route::get('/paiement/delete/{id}', [VenteController::class, 'deletePaiement'])->name('paiement.delete');
        Route::get('/paiement/valider/{id}', [VenteController::class, 'validerPaiement'])->name('paiement.valider');
        Route::get('/paiement/recu/{id}', [VenteController::class, 'recuPaiement'])->name('paiement.recu');

        // Validation globale
        Route::get('/valider/{id}', [VenteController::class, 'valider'])->name('ventes.valider');
    });

    // --- ACHATS ---
    Route::prefix('Achats')->group(function () {
        Route::get('/', [AchatController::class, 'index'])->name('achat.index');
        Route::match(['get', 'post'], '/add-edit/{id?}', [AchatController::class, 'createOrUpdate'])->name('achat.create');
        Route::get('/delete/{id}', [AchatController::class, 'delete'])->name('achats.delete');

        // Détails Achat
        Route::post('/acheter/store', [AchatController::class, 'storeProduit'])->name('acheter.store');
        Route::get('/acheter/delete/{id}', [AchatController::class, 'deleteProduit'])->name('acheter.delete');
        Route::get('/acheter/edit/{id}', [AchatController::class, 'editProduit'])->name('acheter.edit');
        Route::post('/acheter/update/{id}', [AchatController::class, 'updateProduit'])->name('acheter.update');

        // Règlement
        Route::post('/reglement/store', [AchatController::class, 'storeReglement'])->name('reglement.store');
        Route::get('/reglement/edit/{id}', [AchatController::class, 'editReglement'])->name('reglement.edit');
        Route::post('/reglement/update/{id}', [AchatController::class, 'updateReglement'])->name('reglement.update');
        Route::get('/reglement/delete/{id}', [AchatController::class, 'deleteReglement'])->name('reglement.delete');
        Route::get('/reglement/valider/{id}', [AchatController::class, 'validerReglement'])->name('reglement.valider');

        // Validation globale
        Route::get('/valider/{id}', [AchatController::class, 'valider'])->name('achats.valider');
    });

    // --- FERMES & ÉTABLISSEMENT ---
    Route::get('/Mon-Etablissement', [FermeController::class, 'monProfil'])->name('ferme.mon-profil');

    Route::prefix('Fermes')->group(function () {
        Route::get('/', [FermeController::class, 'index'])->name('fermes.index');
        Route::match(['get', 'post'], '/add-edit/{id?}', [FermeController::class, 'storeOrUpdate'])->name('fermes.save');
        Route::get('/delete/{id}', [FermeController::class, 'destroy'])->name('fermes.delete');
        Route::get('/status/{id}', [FermeController::class, 'toggleEtat'])->name('fermes.status');
    });

    // --- UTILISATEURS ---
    Route::prefix('Utilisateurs')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('Users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::match(['get', 'post'], '/save/{id?}', [UserController::class, 'storeOrUpdate'])->name('users.save');
        Route::get('/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');
    });

    // --- TRANSFORMATIONS ---
    Route::get('/Transformations', [TransformationController::class, 'index'])->name('transformations.index');
    Route::get('/Transformations/create', [TransformationController::class, 'create'])->name('transformations.create');
    Route::post('/Transformations/store', [TransformationController::class, 'store'])->name('transformations.action');
    Route::get('/Transformations/add-edit/{id}', [TransformationController::class, 'edit']);
    Route::match(['post', 'get'], '/Transformations/valider/{id}', [TransformationController::class, 'store'])->defaults('emp', 'PRV');
    Route::match(['post', 'delete'], '/Transformations/delete/{id}', [TransformationController::class, 'store'])->defaults('emp', 'D');
    Route::put('/Transformers/update/{trm_id}', [TransformationController::class, 'store'])->defaults('emp', 'PU');

    // --- VACCINATIONS ---
    Route::prefix('Vaccinations')->group(function () {
        Route::get('/', [VaccinationController::class, 'handleAction'])->name('vaccinations.index');
        Route::post('/', [VaccinationController::class, 'handleAction'])->name('vaccinations.handle');
        Route::get('/add-edit/{id?}', [VaccinationController::class, 'handleAction']);
        Route::post('/add-edit/{id?}', [VaccinationController::class, 'handleAction']);
    });

    // --- ALIMENTATIONS, PERTES & COLLECTIONS ---
    Route::match(['get', 'post'], '/Alimentations', [AlimentationController::class, 'handleAction'])->name('alimentation.handle');
    Route::match(['get', 'post'], '/Alimentations/add-edit', [AlimentationController::class, 'handleAction']);

    Route::match(['get', 'post'], '/Pertes', [PerteController::class, 'handleAction'])->name('pertes.handle');
    Route::match(['get', 'post'], '/Pertes/add-edit', [PerteController::class, 'handleAction']);

    Route::match(['get', 'post'], '/Collections', [CollectionController::class, 'handleAction'])->name('collections.handle');
    Route::match(['get', 'post'], '/Collections/add-edit', [CollectionController::class, 'handleAction']);

});