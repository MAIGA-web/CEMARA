@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">

                {{-- ################################################################### --}}
                {{-- ################## Partie Gauche : Liste des Productions ########## --}}
                {{-- ################################################################### --}}
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title text-primary">Suivi des Productions</strong>
                            <a href="{{ route('production.create') }}" class="btn btn-sm btn-primary float-right">
                                <i class="fa fa-plus"></i> Nouvelle Saisie
                            </a>
                        </div>
                        <div class="card-body">
                            @if (session('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="bootstrap-data-table">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Poulailler</th>
                                            <th>Date Saisie</th>
                                            <th>Œufs</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1; @endphp
                                        @foreach ($productions as $p)
                                            @php
                                                $isEstSelectionne = request('prd_id') == $p->id;
                                            @endphp

                                            {{-- On applique un fond léger (table-primary) à toute la ligne si elle est sélectionnée --}}
                                            <tr class="{{ $isEstSelectionne ? 'table-primary font-weight-bold' : '' }}">
                                                <td>{{ $i++ }}</td>
                                                <td>
                                                    {{-- Le bouton du poulailler passe en bleu foncé (btn-primary) s'il est sélectionné --}}
                                                    <a href="{{ route('production.index', ['prd_id' => $p->id]) }}"
                                                        class="btn btn-sm {{ $isEstSelectionne ? 'btn-primary shadow' : 'btn-info' }}"
                                                        title="Détails & Consommations">
                                                        @if ($isEstSelectionne)
                                                            <i class="fa fa-arrow-circle-right mr-1"></i>
                                                        @endif
                                                        {{ $p->poulailler->poul_nom ?? 'Poulailler #' . $p->poul_id }}
                                                    </a>
                                                </td>
                                                <td>{{ $p->created_at->format('d/m/Y') }}</td>
                                                <td><strong>{{ number_format($p->nbr_ouef, 0, ',', ' ') }}</strong></td>
                                                <td class="text-center">
                                                    @if ($p->prodc_etat == 1)
                                                        {{-- Si la Production est validée --}}
                                                        <span class="badge badge-success">
                                                            <i class="fa fa-check"></i> Clôturée
                                                        </span>
                                                    @else
                                                        {{-- Si la Production est modifiable --}}
                                                        <form action="{{ route('production.action') }}" method="POST"
                                                            style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="emp" value="PRV">
                                                            <input type="hidden" name="prd_id"
                                                                value="{{ $p->id }}">
                                                            <input type="hidden" name="fer_id"
                                                                value="{{ $p->fer_id }}">
                                                            <button type="submit" name="valider" value="Oui"
                                                                class="btn btn-sm btn-success"
                                                                onclick="return confirm('Valider et mouvementer définitivement les stocks pour cette production ?')"
                                                                title="Valider la production">
                                                                <i class="fa fa-check-square-o"></i>
                                                            </button>

                                                        </form>
                                                        {{-- Bouton Édition : s'adapte aussi si on est déjà en mode édition sur cette fiche --}}
                                                        <a href="{{ route('production.create', $p->id) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fa fa-pencil text-white"></i>
                                                        </a>
                                                        <form action="{{ route('production.action') }}" method="POST"
                                                            style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="emp" value="D">
                                                            <input type="hidden" name="prd_id"
                                                                value="{{ $p->id }}">
                                                            <button type="submit" name="valider" value="Oui"
                                                                class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Supprimer définitivement cette fiche de production ?')">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- ################################################################### --}}
                {{-- ################## Partie Droite : Onglets & Détails ############## --}}
                {{-- ################################################################### --}}
                <div class="col-md-7">
                    @if (session('error_message'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error_message') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if ($prd)
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <strong class="card-title text-white">
                                    Fiche Production : {{ $prd->poulailler->poul_nom ?? 'Poulailler #' . $prd->poul_id }}
                                    (Du {{ $prd->created_at->format('d/m/Y H:i') }})
                                </strong>
                            </div>
                            <div class="card-body">

                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home"
                                            role="tab" aria-selected="true">Rapport Global</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile"
                                            role="tab" aria-selected="false">Materiaux / Aliments Consommés
                                            ({{ $produires->count() }})</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="pills-tabContent">

                                    {{-- =================================================================== --}}
                                    {{-- Onglet 1 : Rapport Global --}}
                                    {{-- =================================================================== --}}
                                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Date d'Enregistrement :
                                                <span class="badge badge-secondary badge-pill">
                                                    {{ $prd->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Statut de validation :
                                                <span
                                                    class="badge badge-{{ $prd->prodc_etat ? 'success' : 'warning' }} badge-pill">
                                                    {{ $prd->prodc_etat ? 'Validé (Stock mis à jour)' : 'En attente de validation' }}
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Durée du cycle de production :
                                                <strong>{{ $prd->prodc_dure }} Jours</strong>
                                            </li>
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center text-primary">
                                                Œufs récoltés (nbr_ouef) :
                                                <strong>{{ number_format($prd->nbr_ouef, 0, ',', ' ') }} Œufs</strong>
                                            </li>
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center text-secondary">
                                                Total général des œufs en stock :
                                                <strong>{{ number_format($max_oeuf_stock, 0, ',', ' ') }} Œufs</strong>
                                            </li>
                                        </ul>

                                        @if (!$prd->prodc_etat)
                                            <div class="alert alert-warning mt-3">
                                                <i class="fa fa-warning"></i> <strong>Attention :</strong> Cette production
                                                n'est pas encore validée. Les matières premières ne sont pas encore déduites
                                                et les œufs récoltés ne sont pas encore intégrés à votre stock global.
                                            </div>
                                        @endif
                                    </div>

                                    {{-- =================================================================== --}}
                                    {{-- Onglet 2 : Produits consommés / Produits Utilisés (table produires) --}}
                                    {{-- =================================================================== --}}
                                    <div class="tab-pane fade" id="pills-profile" role="tabpanel">

                                        {{-- Formulaire pour ajouter une ligne de consommation (si non clôturé) --}}
                                        @if (!$prd->prodc_etat)
                                            <h5 class="mb-3 text-primary">Selectionner une produiction</h5>

                                            {{-- Utilisation de ta route unique storeAction avec l'identifiant 'PU' ou 'C' selon tes besoins --}}
                                            {{-- <form action="{{ route('production.action') }}" method="POST"
                                                class="form-inline mb-4 p-3 bg-light rounded" style="gap: 10px;">
                                                @csrf
                                                <input type="hidden" name="emp" value="C">
                                                Déclenche l'ajout/composants
                                                <input type="hidden" name="prd_id" value="{{ $prd->id }}">
                                                <input type="hidden" name="nbr_ouef" value="{{ $prd->nbr_ouef }}">
                                                <input type="hidden" name="poul_id" value="{{ $prd->poul_id }}">
                                                <input type="hidden" name="prodc_dure" value="{{ $prd->prodc_dure }}">

                                                <select name="pro_id" class="form-control form-control-sm" required
                                                    style="flex: 2;">
                                                    <option value="">-- Choisir l'aliment ou vaccin --</option>
                                                    @foreach ($produits as $p)
                                                        pro_etat = 0 représentent les intrants/aliments consommés
                                                        @if ($p->pro_etat == 0)
                                                            <option value="{{ $p->id }}">{{ $p->pro_nom }}
                                                                ({{ $p->pro_type }}) (Stock dispos: {{ $p->pro_stock }})
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>

                                                <input type="number" step="0.01" name="cprd_qte"
                                                    class="form-control form-control-sm" placeholder="Quantité Utilisée"
                                                    required style="flex: 1; max-width: 150px;">

                                                <button type="submit" name="valider" value="Valider"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fa fa-plus"></i> Insérer
                                                </button>
                                            </form> --}}
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fa fa-lock"></i> Cette production est clôturée. Les consommations
                                                associées sont verrouillées.
                                            </div>
                                        @endif

                                        <hr>
                                        <h5 class="mb-3">Détails des consommations réelles</h5>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>N°</th>
                                                        <th>Intrant / Produit</th>
                                                        <th>Type</th>
                                                        <th>Quantité Consommée</th>
                                                        @if (!$prd->prodc_etat)
                                                            <th class="text-center">Action</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $i = 1; @endphp
                                                    @forelse($produires as $pv)
                                                        <tr>
                                                            <td>{{ $i++ }}</td>
                                                            <td>{{ $pv->produit->pro_nom ?? 'Inconnu' }}</td>
                                                            <td>{{ $pv->produit->pro_type ?? 'N/A' }}</td>
                                                            <td><strong>{{ $pv->prdr_qte }}</strong></td>

                                                            @if (!$prd->prodc_etat)
                                                                <td class="text-center">
                                                                    {{-- Formulaire de modification rapide de quantité (Case PU de ton controleur) --}}
                                                                    <form action="{{ route('production.action') }}"
                                                                        method="POST" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="emp"
                                                                            value="PU">
                                                                        <input type="hidden" name="cprd_id"
                                                                            value="{{ $pv->id }}">
                                                                        <input type="number" name="prdr_qte"
                                                                            value="{{ $pv->prdr_qte }}" step="0.01"
                                                                            style="width:60px; font-size:12px;" required>
                                                                        <button type="submit" name="valider"
                                                                            value="Valider" class="btn btn-xs btn-warning"
                                                                            title="Mettre à jour la qte">
                                                                            <i class="fa fa-edit text-white"></i>
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Aucun
                                                                intrant ou aliment n'est enregistré pour cette session de
                                                                production.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div> {{-- Fin tab-content --}}
                            </div> {{-- Fin card-body --}}
                        </div> {{-- Fin card --}}
                    @else
                        {{-- État initial si aucune production n'est cliquée --}}
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fa fa-cubes fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Sélectionnez une fiche de production à gauche pour analyser ses
                                    consommations et détails.</h4>
                            </div>
                        </div>
                    @endif
                </div>
                {{-- ################################################################### --}}
                {{-- ################## FIN La partie onglets ########################## --}}
                {{-- ################################################################### --}}

            </div>
        </div>
    </div>

    {{-- Script pour préserver l'activation de l'onglet actif au rechargement --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');

            if (activeTab) {
                const tabLink = document.querySelector(`a[href="#${activeTab}"]`);
                if (tabLink) {
                    $(tabLink).tab('show');
                }
            }
        });
    </script>
@endsection
