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

                                            <tr class="{{ $isEstSelectionne ? 'table-primary font-weight-bold' : '' }}">
                                                <td>{{ $i++ }}</td>
                                                <td>
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
                                                    @if (!$p->prodc_etat || Auth::user()->user_etat == 1)
                                                        {{-- Si non validé OU si Admin (user_etat = 1) --}}
                                                        @if (!$p->prodc_etat)
                                                            {{-- Bouton de validation rapide (si pas encore validé) --}}
                                                            <form action="{{ route('production.action') }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <input type="hidden" name="emp" value="PRV">
                                                                <input type="hidden" name="prd_id" value="{{ $p->id }}">
                                                                <input type="hidden" name="fer_id" value="{{ $p->fer_id }}">
                                                                <button type="submit" name="valider" value="Oui"
                                                                    class="btn btn-sm btn-success"
                                                                    onclick="return confirm('Valider et mouvementer définitivement les stocks pour cette production ?')"
                                                                    title="Valider la production">
                                                                    <i class="fa fa-check-square-o"></i>
                                                                </button>
                                                            </form>
                                                              @else<span class="badge badge-success"><i class="fa fa-check"></i> Validé</span>
                                                        @endif


                                                        {{-- Bouton Édition --}}
                                                        <a href="{{ route('production.create', $p->id) }}"
                                                            class="btn btn-sm btn-warning" title="Modifier">
                                                            <i class="fa fa-pencil text-white"></i>
                                                        </a>

                                                        {{-- Bouton Suppression --}}
                                                        <form action="{{ route('production.action') }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="emp" value="D">
                                                            <input type="hidden" name="prd_id" value="{{ $p->id }}">
                                                            <button type="submit" name="valider" value="Oui"
                                                                class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Supprimer définitivement cette fiche de production ?')"
                                                                title="Supprimer">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        {{-- Production clôturée et utilisateur standard --}}
                                                        <span class="badge badge-success">
                                                            <i class="fa fa-check"></i> Clôturée
                                                        </span>
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
                                            role="tab" aria-selected="false">Matériaux / Aliments Consommés
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
                                                <span class="badge badge-{{ $prd->prodc_etat ? 'success' : 'warning' }} badge-pill">
                                                    {{ $prd->prodc_etat ? 'Validé (Stock mis à jour)' : 'En attente de validation' }}
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Durée du cycle de production :
                                                <strong>{{ $prd->prodc_dure }} Jours</strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center text-primary">
                                                Œufs récoltés (nbr_ouef) :
                                                <strong>{{ number_format($prd->nbr_ouef, 0, ',', ' ') }} Œufs</strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center text-secondary">
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

                                        @if (!$prd->prodc_etat || Auth::user()->user_etat == 1)
                                            {{-- Autorisé si non validé OU si Administrateur --}}
                                            @if ($prd->prodc_etat)
                                                <div class="alert alert-warning py-2 mb-3">
                                                    <i class="fa fa-exclamation-triangle"></i> Mode Administrateur : Vous modifiez une production déjà clôturée.
                                                </div>
                                            @endif
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fa fa-lock"></i> Cette production est clôturée. Seul un administrateur peut modifier les consommations associées.
                                            </div>
                                        @endif

                                        <h5 class="mb-3">Détails des consommations réelles</h5>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>N°</th>
                                                        <th>Intrant / Produit</th>
                                                        <th>Type</th>
                                                        <th>Quantité Consommée</th>
                                                        <th class="text-center">Action</th>
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
                                                            <td class="text-center">
                                                                @if (!$prd->prodc_etat || Auth::user()->user_etat == 1)
                                                                    {{-- Formulaire de mise à jour rapide visible par tous avant validation, ou uniquement admin après --}}
                                                                    <form action="{{ route('production.action') }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="emp" value="PU">
                                                                        <input type="hidden" name="cprd_id" value="{{ $pv->id }}">
                                                                        <input type="number" name="prdr_qte" value="{{ $pv->prdr_qte }}"
                                                                            step="0.01" style="width:70px; font-size:12px;" required>
                                                                        <button type="submit" name="valider" value="Valider"
                                                                            class="btn btn-xs btn-warning" title="Mettre à jour la quantité">
                                                                            <i class="fa fa-edit text-white"></i>
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <i class="fa fa-lock text-muted" title="Consommation verrouillée"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Aucun intrant ou aliment n'est enregistré pour cette session de production.</td>
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
                        {{-- État initial si aucune production n'est sélectionnée --}}
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fa fa-cubes fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Sélectionnez une fiche de production à gauche pour analyser ses consommations et détails.</h4>
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