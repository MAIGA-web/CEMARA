<?php

use App\Http\Controllers\AchatController;
use App\Http\Controllers\AlimentationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\PoulaillerController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ModeController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\VeterinaireController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FermeController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\TransformationController;
use App\Http\Controllers\VaccinationController;
use App\Http\Controllers\PerteController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\LotSuiviController;




// --- ROUTES PUBLIQUES ---
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
Route::post('/inscription', [AuthController::class, 'register']);

// --- ROUTES PROTÉGÉES (Connecté) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/SuperAdmin/choix', [FermeController::class, 'index'])->name('SuperAdmin.index');
    // Action de choix
    Route::post('/SuperAdmin/choisir', [FermeController::class, 'choisir'])->name('superadmin.choisir');




    // Gestion des Lots et Suivi de croissance
    Route::get('/Lots', [LotSuiviController::class, 'index'])->name('lots.index');
    Route::post('/Lots/store', [LotSuiviController::class, 'storeLot'])->name('lots.storeLot');
    Route::post('/Lots/suivi/store', [LotSuiviController::class, 'storeSuivi'])->name('lots.storeSuivi');
    Route::post('/Lots/cloturer/{id}', [LotSuiviController::class, 'cloturerLot'])->name('lots.cloturer');
    Route::delete('/Lots/suivi/delete/{id}', [LotSuiviController::class, 'deleteSuivi'])->name('lots.deleteSuivi');
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Clients
    Route::get('Clients', [ClientController::class, 'clients']);
    Route::match(['get', 'post'], 'Clients/add-edit/{id?}', [ClientController::class, 'create']);
    Route::get('Clients/delete/{id}', [ClientController::class, 'suppression']);

    // Fournisseurs
    Route::get('Fournisseurs', [FournisseurController::class, 'fournisseurs']);
    Route::match(['get', 'post'], 'Fournisseurs/add-edit/{id?}', [FournisseurController::class, 'create']);
    Route::get('Fournisseurs/delete/{id}', [FournisseurController::class, 'suppression']);

    // Vétérinaires
    Route::get('Veterinaires', [VeterinaireController::class, 'veterinaires']);
    Route::match(['get', 'post'], 'Veterinaires/add-edit/{id?}', [VeterinaireController::class, 'create']);
    Route::get('Veterinaires/delete/{id}', [VeterinaireController::class, 'suppression']);

    // Produits
    Route::get('Produits', [ProduitController::class, 'produits']);
    Route::match(['get', 'post'], 'Produits/add-edit/{id?}', [ProduitController::class, 'create']);
    Route::get('Produits/delete/{id}', [ProduitController::class, 'suppression']);

    // Poulaillers
    Route::get('Poulaillers', [PoulaillerController::class, 'poulaillers']);
    Route::match(['get', 'post'], 'Poulaillers/add-edit/{id?}', [PoulaillerController::class, 'create']);
    Route::get('Poulaillers/delete/{id}', [PoulaillerController::class, 'suppression']);

    // Matieres
    Route::get('Matieres', [MatiereController::class, 'matieres']);
    Route::match(['get', 'post'], 'Matieres/add-edit/{id?}', [MatiereController::class, 'create']);
    Route::get('Matieres/delete/{id}', [MatiereController::class, 'suppression']);

    // Modes
    Route::get('Modes', [ModeController::class, 'modes']);
    Route::match(['get', 'post'], 'Modes/add-edit/{id?}', [ModeController::class, 'create']);
    Route::get('Modes/delete/{id}', [ModeController::class, 'suppression']);

    // Route::get('Productions', [ProductionController::class, 'index']);
    Route::get('Productions', [ProductionController::class, 'index'])->name('production.index');
    Route::get('/production/create/{id?}', [ProductionController::class, 'create'])->name('production.create');;
    Route::post('/production/action', [ProductionController::class, 'storeAction'])->name('production.action');


    // Gestion des Ventes (Prefixe remis sur /Ventes)
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
    Route::get('/Mon-Etablissement', [FermeController::class, 'monProfil'])->name('ferme.mon-profil');
    // --- GESTION GLOBALE (Pour l'administrateur du logiciel) ---
    Route::prefix('Fermes')->group(function () {
        // Liste de toutes les fermes
        Route::get('/', [FermeController::class, 'index'])->name('fermes.index');
        // Formulaire ajout/edit (via ID)
        Route::match(['get', 'post'], '/add-edit/{id?}', [FermeController::class, 'storeOrUpdate'])->name('fermes.save');
        // Actions rapides
        Route::get('/delete/{id}', [FermeController::class, 'destroy'])->name('fermes.delete');
        Route::get('/status/{id}', [FermeController::class, 'toggleEtat'])->name('fermes.status');
    });

    Route::prefix('Utilisateurs')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('Users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::match(['get', 'post'], '/save/{id?}', [UserController::class, 'storeOrUpdate'])->name('users.save');
        Route::get('/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');
    });

    // Gestion des Achats (Prefixe remis sur /Ventes)
    Route::prefix('Achats')->group(function () {
        Route::get('/', [AchatController::class, 'index'])->name('achat.index');
        Route::match(['get', 'post'], '/add-edit/{id?}', [AchatController::class, 'createOrUpdate'])->name('achat.create');
        Route::get('/delete/{id}', [AchatController::class, 'delete'])->name('achats.delete');

        // Détails Vente
        Route::post('/acheter/store', [AchatController::class, 'storeProduit'])->name('acheter.store');
        Route::get('/acheter/delete/{id}', [AchatController::class, 'deleteProduit'])->name('acheter.delete');
        Route::get('/acheter/edit/{id}', [AchatController::class, 'editProduit'])->name('acheter.edit');
        Route::post('/acheter/update/{id}', [AchatController::class, 'updateProduit'])->name('acheter.update');

        // Reglement
        Route::post('/reglement/store', [AchatController::class, 'storeReglement'])->name('reglement.store');
        Route::get('/reglement/edit/{id}', [AchatController::class, 'editReglement'])->name('reglement.edit');
        Route::post('/reglement/update/{id}', [AchatController::class, 'updateReglement'])->name('reglement.update');
        Route::get('/reglement/delete/{id}', [AchatController::class, 'deleteReglement'])->name('reglement.delete');
        Route::get('/reglement/valider/{id}', [AchatController::class, 'validerReglement'])->name('reglement.valider');
        // Route::get('/reglement/recu/{id}', [AchatController::class, 'recuReglement'])->name('reglement.recu');

        // Validation globale
        Route::get('/valider/{id}', [AchatController::class, 'valider'])->name('achats.valider');
    });

    Route::get('/Transformations', [TransformationController::class, 'index'])->name('transformations.index');
    Route::get('/Transformations/create', [TransformationController::class, 'create'])->name('transformations.create');

    // Toutes les actions de traitement (C, U, PU, PRV, D) passent par cette route unique POST
    Route::post('/Transformations/store', [TransformationController::class, 'store'])->name('transformations.action');

    // Raccourcis d'URLs si vos boutons pointent en direct
    Route::get('/Transformations/add-edit/{id}', [TransformationController::class, 'edit']);
    Route::match(['post', 'get'], '/Transformations/valider/{id}', [TransformationController::class, 'store'])->defaults('emp', 'PRV');
    Route::match(['post', 'delete'], '/Transformations/delete/{id}', [TransformationController::class, 'store'])->defaults('emp', 'D');
    Route::put('/Transformers/update/{trm_id}', [TransformationController::class, 'store'])->defaults('emp', 'PU');

    Route::prefix('Vaccinations')->group(function () {
        // Le GET principal appelle handleAction
        Route::get('/', [VaccinationController::class, 'handleAction'])->name('vaccinations.index');

        // Le POST (envoi du formulaire) appelle aussi handleAction
        Route::post('/', [VaccinationController::class, 'handleAction'])->name('vaccinations.handle');

        // Si votre design utilise l'URL /add-edit (comme pour les Produits)
        Route::get('/add-edit', [VaccinationController::class, 'handleAction']);
        Route::post('/add-edit', [VaccinationController::class, 'handleAction']);
        Route::get('/add-edit/{id}', [VaccinationController::class, 'handleAction']);
        Route::post('/add-edit/{id}', [VaccinationController::class, 'handleAction']);
    });

    Route::match(['get', 'post'], '/Alimentations', [AlimentationController::class, 'handleAction'])->name('alimentation.handle');
    Route::match(['get', 'post'], '/Alimentations/add-edit', [AlimentationController::class, 'handleAction']);


    Route::match(['get', 'post'], '/Pertes', [PerteController::class, 'handleAction'])->name('pertes.handle');
    Route::match(['get', 'post'], '/Pertes/add-edit', [PerteController::class, 'handleAction']);


    // Route pour l'affichage principal, l'ajout et la validation (GET & POST)
    Route::match(['get', 'post'], '/Collections', [CollectionController::class, 'handleAction'])->name('collections.handle');

    // Route spécifique pour l'ouverture du formulaire "Ajouter / Modifier"
    Route::match(['get', 'post'], '/Collections/add-edit', [CollectionController::class, 'handleAction']);


    // Gestion des Lots et Suivi de croissance
    Route::get('/Lots', [LotSuiviController::class, 'index'])->name('lots.index');
    Route::post('/Lots/store', [LotSuiviController::class, 'storeLot'])->name('lots.storeLot');
    Route::post('/Lots/suivi/store', [LotSuiviController::class, 'storeSuivi'])->name('lots.storeSuivi');
    Route::post('/Lots/cloturer/{id}', [LotSuiviController::class, 'cloturerLot'])->name('lots.cloturer');
    Route::delete('/Lots/suivi/delete/{id}', [LotSuiviController::class, 'deleteSuivi'])->name('lots.deleteSuivi');
    Route::put('/Lots/update/{id}', [LotSuiviController::class, 'updateLot'])->name('lots.updateLot');
    Route::delete('/Lots/delete/{id}', [LotSuiviController::class, 'deleteLot'])->name('lots.deleteLot');
    Route::put('/Lots/suivi/update/{id}', [LotSuiviController::class, 'updateSuivi'])->name('lots.updateSuivi');
});
