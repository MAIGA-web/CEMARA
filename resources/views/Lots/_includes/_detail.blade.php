<div class="col-xl-8 col-lg-7">
    @if ($lotSelectionne)
        {{-- En-tête & Fiche d'identité --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1">Suivi Technique : {{ $lotSelectionne->lot_code }}
                        </h4>
                        <p class="text-muted mb-0">Poulailler affecté :
                            <strong>{{ $lotSelectionne->poulailler->poul_nom ?? 'N/A' }}</strong>
                        </p>
                    </div>
                    @if ($lotSelectionne->lot_actif)
                        <form action="{{ route('lots.cloturer', $lotSelectionne->id) }}" method="POST"
                            onsubmit="return confirm('Voulez-vous vraiment clôturer ce lot définitivement ? (Fin de bande)');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm font-weight-bold">
                                <i class="fa fa-lock mr-1"></i> Clôturer / Vendre la Bande
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Widgets Statistiques --}}
                <div class="row text-center mt-4">
                    <div class="col-md-3 col-6 mb-2">
                        <div class="bg-light p-3 rounded shadow-xs">
                            <small class="text-muted font-weight-bold text-uppercase font-xs d-block mb-1">Âge
                                Actuel</small>
                            <strong class="text-dark h4 font-weight-bold mb-0">{{ $lotSelectionne->age_poussins }}
                                J</strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="bg-light p-3 rounded shadow-xs">
                            <small class="text-muted font-weight-bold text-uppercase font-xs d-block mb-1">Effectif
                                Vivant</small>
                            <strong
                                class="text-success h4 font-weight-bold mb-0">{{ number_format($lotSelectionne->reste_vivants, 0, ',', ' ') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="bg-light p-3 rounded shadow-xs">
                            <small class="text-muted font-weight-bold text-uppercase font-xs d-block mb-1">Pertes
                                Cumulées</small>
                            <strong
                                class="text-danger h4 font-weight-bold mb-0">{{ number_format($lotSelectionne->total_morts, 0, ',', ' ') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="bg-light p-3 rounded shadow-xs">
                            <small class="text-muted font-weight-bold text-uppercase font-xs d-block mb-1">Sortie
                                Prévue</small>
                            <strong
                                class="text-warning font-weight-bold font-sm d-block mt-1">{{ $lotSelectionne->lot_date_sortie_prevue->format('d/m/Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Nav Tabs --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom p-0">
                <ul class="nav nav-tabs card-header-tabs m-0 px-3" id="lotTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active py-3 font-weight-bold" id="historique-tab" data-toggle="tab"
                            href="#pills-historique" role="tab">
                            <i class="fa fa-history mr-1"></i> Journal Quotidien
                        </a>
                    </li>
                    @if ($lotSelectionne->lot_actif)
                        <li class="nav-item">
                            <a class="nav-link py-3 font-weight-bold text-primary" id="saisie-tab" data-toggle="tab"
                                href="#pills-saisie" role="tab">
                                <i class="fa fa-pencil mr-1"></i> Enregistrer le Rapport du Jour
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="lotTabsContent">

                    {{-- TAB 1 : HISTORIQUE DES RAPPORTS --}}
                    <div class="tab-pane fade show active" id="pills-historique" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped border-0 mb-0" style="font-size: 13px;">
                                <thead class="bg-light text-secondary font-weight-bold text-uppercase font-xs">
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-center">Mortalité</th>
                                        <th class="text-center">Consommation (Kg)</th>
                                        <th>État Sanitaire</th>
                                        <th>Observations / Suivis</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($suivisJournaliers as $sj)
                                        <tr>
                                            <td class="align-middle font-weight-bold text-dark">
                                                {{ $sj->suivi_date->format('d/m/Y') }}</td>
                                            <td class="align-middle text-center">
                                                @if ($sj->morts_jour > 0)
                                                    <span
                                                        class="badge badge-danger px-2 py-1 font-weight-bold">{{ $sj->morts_jour }}
                                                        Mort(s)</span>
                                                @else
                                                    <span class="text-success font-weight-bold">0</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center font-weight-bold text-dark">
                                                {{ number_format($sj->consommation_aliment, 2, ',', ' ') }} Kg</td>
                                            <td class="align-middle">
                                                <span
                                                    class="badge {{ $sj->etat_sante == 'Sain' ? 'badge-success' : 'badge-warning' }} p-1 px-2">
                                                    {{ $sj->etat_sante }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-muted">{{ $sj->observations ?? '-' }}</td>
                                            <td class="align-middle text-right" style="white-space: nowrap;">
                                                {{-- Bouton Modifier le Suivi --}}
                                                <button class="btn btn-xs btn-outline-primary py-0 px-2 mr-1"
                                                    data-toggle="modal"
                                                    data-target="#modalEditSuivi{{ $sj->id }}">
                                                    <i class="fa fa-pencil"></i>
                                                </button>

                                                {{-- Formulaire Supprimer le Suivi --}}
                                                <form action="{{ route('lots.deleteSuivi', $sj->id) }}" method="POST"
                                                    onsubmit="return confirm('Supprimer ce rapport quotidien ?');"
                                                    class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-xs btn-outline-danger py-0 px-2"
                                                        style="font-size: 11px;">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>

                                                {{-- MODAL INTERNE : MODIFIER UN RAPPORT QUOTIDIEN --}}
                                                <div class="modal fade" id="modalEditSuivi{{ $sj->id }}"
                                                    tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content border-0 shadow text-left">
                                                            <div class="modal-header bg-primary text-white p-3">
                                                                <h5 class="modal-title text-white font-weight-bold"><i
                                                                        class="fa fa-edit"></i> Modifier le Rapport du
                                                                    {{ $sj->suivi_date->format('d/m/Y') }}</h5>
                                                                <button type="button" class="close text-white"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form action="{{ route('lots.updateSuivi', $sj->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body p-4">
                                                                    <div class="row">
                                                                        <div class="form-group col-md-6">
                                                                            <label
                                                                                class="font-weight-bold text-secondary font-xs text-uppercase">Date
                                                                                du suivi</label>
                                                                            <input type="date" name="suivi_date"
                                                                                class="form-control"
                                                                                value="{{ $sj->suivi_date->format('Y-m-d') }}"
                                                                                required>
                                                                        </div>
                                                                        <div class="form-group col-md-6">
                                                                            <label
                                                                                class="font-weight-bold text-secondary font-xs text-uppercase">État
                                                                                Sanitaire</label><br>
                                                                            <select name="etat_sante"
                                                                                class="form-control custom-select"
                                                                                required>
                                                                                <option value="Sain"
                                                                                    {{ $sj->etat_sante == 'Sain' ? 'selected' : '' }}>
                                                                                    🟢 Sain / Normal</option>
                                                                                <option value="Stressé"
                                                                                    {{ $sj->etat_sante == 'Stressé' ? 'selected' : '' }}>
                                                                                    🟡 Stressé</option>
                                                                                <option value="Malade"
                                                                                    {{ $sj->etat_sante == 'Malade' ? 'selected' : '' }}>
                                                                                    🔴 Malade</option>
                                                                                <option value="Alerte"
                                                                                    {{ $sj->etat_sante == 'Alerte' ? 'selected' : '' }}>
                                                                                    ⚠️ Alerte</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group col-md-6">
                                                                            <label
                                                                                class="font-weight-bold text-secondary font-xs text-uppercase">Mortalité
                                                                                (Têtes)
                                                                            </label>
                                                                            <input type="number" name="morts_jour"
                                                                                min="0" class="form-control"
                                                                                value="{{ $sj->morts_jour }}"
                                                                                required>
                                                                        </div>
                                                                        <div class="form-group col-md-6">
                                                                            <label
                                                                                class="font-weight-bold text-secondary font-xs text-uppercase">Aliment
                                                                                (Kg)</label>
                                                                            <input type="number" step="0.01"
                                                                                min="0"
                                                                                name="consommation_aliment"
                                                                                class="form-control"
                                                                                value="{{ $sj->consommation_aliment }}"
                                                                                required>
                                                                        </div>
                                                                        <div class="form-group col-md-12 mb-0">
                                                                            <label
                                                                                class="font-weight-bold text-secondary font-xs text-uppercase">Observations</label>
                                                                            <textarea name="observations" rows="3" class="form-control">{{ $sj->observations }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light p-3">
                                                                    <button type="button"
                                                                        class="btn btn-secondary font-weight-bold"
                                                                        data-dismiss="modal">Annuler</button>
                                                                    <button type="submit"
                                                                        class="btn btn-primary font-weight-bold">Appliquer
                                                                        les modifications</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- FIN MODAL --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">Aucun
                                                enregistrement quotidien pour ce lot.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2 : FORMULAIRE DE SAISIE RAPIDE --}}
                    @if ($lotSelectionne->lot_actif)
                        <div class="tab-pane fade" id="pills-saisie" role="tabpanel">
                            <form action="{{ route('lots.storeSuivi') }}" method="POST"
                                class="bg-light p-3 rounded border">
                                @csrf
                                <input type="hidden" name="lot_id" value="{{ $lotSelectionne->id }}">

                                <div class="row shadow-xs">
                                    <div class="form-group col-md-6">
                                        <label class="font-weight-bold text-secondary font-xs text-uppercase">Date du
                                            suivi</label>
                                        <input type="date" name="suivi_date" class="form-control"
                                            value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="font-weight-bold text-secondary font-xs text-uppercase">État
                                            Sanitaire Global</label>
                                        <select name="etat_sante" class="form-control custom-select" required>
                                            <option value="Sain" selected>🟢 Sain / Normal</option>
                                            <option value="Stressé">🟡 Stressé (Chaleur / Bruit)</option>
                                            <option value="Malade">🔴 Signes de maladie détectés</option>
                                            <option value="Alerte">⚠️ Situation Critique</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="font-weight-bold text-secondary font-xs text-uppercase">Mortalité
                                            du jour (Têtes)</label>
                                        <input type="number" name="morts_jour" min="0" class="form-control"
                                            placeholder="0" value="0" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="font-weight-bold text-secondary font-xs text-uppercase">Aliment
                                            consommé (Kg)</label>
                                        <input type="number" step="0.01" min="0"
                                            name="consommation_aliment" class="form-control" placeholder="0.00"
                                            required>
                                    </div>
                                    <div class="form-group col-md-12 mb-3">
                                        <label
                                            class="font-weight-bold text-secondary font-xs text-uppercase">Observations
                                            / Symptômes / Actions</label>
                                        <textarea name="observations" rows="3" class="form-control"
                                            placeholder="Ex: Nettoyage des abreuvoirs effectué, litière propre..."></textarea>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                                        <i class="fa fa-save mr-1"></i> Enregistrer le Rapport
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @else
        {{-- Écran d'attente si aucun lot n'est sélectionné --}}
        <div class="card shadow-sm border-0 h-100 d-flex align-items-center justify-content-center text-center p-5"
            style="min-height: 50vh;">
            <div class="text-muted">
                <i class="fa fa-cube text-light mb-3" style="font-size: 70px;"></i>
                <h5 class="font-weight-bold text-secondary">Aucun lot sélectionné</h5>
                <p class="font-sm px-4">Choisissez un lot dans le panneau de gauche pour consulter son historique de
                    croissance, saisir les indicateurs ou clôturer la bande.</p>
                </p>
            </div>
    @endif;
</div>
