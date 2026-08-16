@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">

                {{-- ################################################################### --}}
                {{-- ################## La partie Transformation ####################### --}}
                {{-- ################################################################### --}}
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title text-primary">Gestion des Transformations</strong>
                            <a href="{{ route('transformations.create') }}" class="btn btn-sm btn-primary float-right">
                                <i class="fa fa-plus"></i> Nouvelle Transformation
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
                                            <th>Matière</th>
                                            <th>Qté (Kg/T)</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      @forelse ($transformations as $key => $t)
    @php
        $isEstSelectionne = request('trans_id') == $t->id;
        $isSuperAdmin = Auth::user()->user_etat == 1;
        $isValidated = (bool) $t->trans_etat;
    @endphp

    <tr class="{{ $isEstSelectionne ? 'table-primary font-weight-bold' : '' }}">
        <td>{{ $key + 1 }}</td>
        <td>
            <a href="{{ url('/Transformations?trans_id=' . $t->id) }}"
                class="btn btn-sm {{ $isEstSelectionne ? 'btn-primary shadow' : 'btn-info' }}"
                title="Voir les détails">
                @if ($isEstSelectionne)
                    <i class="fa fa-arrow-circle-right mr-1"></i>
                @endif
                {{ $t->matiere->ma_nom ?? 'Matière inconnue' }}
                {{ $t->matiere->ma_type ?? '' }}
            </a>
        </td>
        <td>{{ number_format($t->trans_qte, 2, ',', ' ') }}</td>
        <td class="text-center" style="white-space: nowrap;">
            
            {{-- CAS 1 : Non validée -> On affiche le bouton de validation --}}
            @if (!$isValidated)
                <form action="{{ url('/Transformations/store') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="emp" value="PRV">
                    <input type="hidden" name="trans_id" value="{{ $t->id }}">
                    <button type="submit" class="btn btn-sm btn-success"
                        onclick="return confirm('Valider et déduire définitivement les stocks ?')"
                        title="Valider la transformation">
                        <i class="fa fa-check-square-o"></i>
                    </button>
                </form>
            @else
                {{-- CAS 2 : Déjà validée -> On affiche le badge Validée --}}
                <span class="badge badge-success mr-1">
                    <i class="fa fa-check"></i> Validée
                </span>
            @endif

            {{-- Boutons Modifier & Supprimer : Visibles si NON validé OU si SUPER ADMIN --}}
            @if (!$isValidated || $isSuperAdmin)
                {{-- Bouton Modifier --}}
                <a href="{{ url('/Transformations/add-edit/' . $t->id) }}"
                    class="btn btn-sm btn-warning"
                    title="Modifier la quantité injectée">
                    <i class="fa fa-pencil text-white"></i>
                </a>

                {{-- Bouton Supprimer --}}
                <form action="{{ url('/Transformations/store') }}" method="POST" style="display:inline;"
                    onsubmit="return confirm('Supprimer cette transformation ainsi que ses rendements ?')">
                    @csrf
                    <input type="hidden" name="emp" value="D">
                    <input type="hidden" name="trans_id" value="{{ $t->id }}">
                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            @endif

        </td>
    </tr>
@empty
    
        <span class="text-center text-muted py-3">Aucune transformation enregistrée.</span>
    
@endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ################################################################### --}}
                {{-- ################## Fin de la partie Transformation ################ --}}
                {{-- ################################################################### --}}


                {{-- ################################################################### --}}
                {{-- ################## La partie Onglets Détails ###################### --}}
                {{-- ################################################################### --}}
                <div class="col-md-7">
                    @if (session('error_message'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error_message') }}
                            <button type="button" class="close" data-toggle="alert">&times;</button>
                        </div>
                    @endif

                    @if ($transformation_selectionnee)
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <strong class="card-title text-white">
                                    Détails Transformation N° {{ $transformation_selectionnee->id }}
                                    [{{ $transformation_selectionnee->matiere->ma_nom ?? 'N/A' }}]
                                </strong>
                            </div>
                            <div class="card-body">

                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-home-tab" data-toggle="pill" href="#pills-home"
                                            role="tab" aria-controls="pills-home" aria-selected="true">Info Générale</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-products-tab" data-toggle="pill"
                                            href="#pills-products" role="tab" aria-controls="pills-products"
                                            aria-selected="false">Produits Obtenus ({{ count($liaisons_transformer) }})</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="pills-tabContent">

                                    {{-- Onglet 1 : Informations Générales --}}
                                    <div class="tab-pane fade" id="pills-home" role="tabpanel"
                                        aria-labelledby="pills-home-tab">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Date & Heure opération :
                                                <span class="badge badge-secondary badge-pill">
                                                    {{ $transformation_selectionnee->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Matière Première utilisée :
                                                <strong>{{ $transformation_selectionnee->matiere->ma_nom ?? 'N/A' }}
                                                    ({{ $transformation_selectionnee->matiere->ma_type ?? 'N/A' }})</strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Quantité injectée :
                                                <span class="badge badge-primary badge-pill">
                                                    {{ number_format($transformation_selectionnee->trans_qte, 2, ',', ' ') }}
                                                    Kg
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Statut de l'opération :
                                                <span
                                                    class="badge badge-{{ $transformation_selectionnee->trans_etat ? 'success' : 'warning' }} badge-pill">
                                                    {{ $transformation_selectionnee->trans_etat ? 'Validée & Stocks appliqués' : 'En attente de validation' }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Onglet 2 : Produits Finis issus de la Transformation --}}
                                    <div class="tab-pane fade show active" id="pills-products" role="tabpanel"
                                        aria-labelledby="pills-products-tab">

                                        @if (!$transformation_selectionnee->trans_etat)
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> Les rendements théoriques sont
                                                pré-calculés à 97%. Ajustez-les ci-dessous avant validation.
                                            </div>
                                        @elseif(Auth::user()->user_etat == 1)
                                            <div class="alert alert-warning py-2">
                                                <i class="fa fa-exclamation-triangle"></i> Mode Administrateur : Vous modifiez les rendements d'une transformation déjà clôturée.
                                            </div>
                                        @else
                                            <div class="alert alert-secondary">
                                                <i class="fa fa-lock"></i> Opération clôturée. Les rendements réels de
                                                cette transformation sont verrouillés.
                                            </div>
                                        @endif

                                        <h5 class="mb-3 mt-4">Rendement de la Transformation</h5>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>N°</th>
                                                        <th>Produit Fini</th>
                                                        <th>Type</th>
                                                        <th>Quantité Réelle</th>
                                                        <th class="text-center">Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $totalProduit = 0; @endphp
                                                    @forelse($liaisons_transformer as $key => $lt)
                                                        @php $totalProduit += $lt->trme_qte; @endphp
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $lt->produit->pro_nom ?? 'N/A' }}</td>
                                                            <td>{{ $lt->produit->pro_type ?? 'N/A' }}</td>
                                                            <td>
                                                                @if (!$transformation_selectionnee->trans_etat || Auth::user()->user_etat == 1)
                                                                    {{-- Permet la modification avant validation pour tous, et après validation seulement pour l'admin --}}
                                                                    <form action="{{ url('/Transformations/store') }}"
                                                                        method="POST" class="form-inline d-inline-block">
                                                                        @csrf
                                                                        <input type="hidden" name="emp" value="PU">
                                                                        <input type="hidden" name="trm_id" value="{{ $lt->id }}">

                                                                        <input type="number" step="0.01"
                                                                            name="trme_qte"
                                                                            class="form-control form-control-sm text-center"
                                                                            value="{{ $lt->trme_qte }}"
                                                                            style="width: 100px; max-height:30px;"
                                                                            min="0">
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-warning py-0"
                                                                            title="Sauvegarder l'ajustement"
                                                                            style="height: 30px;">
                                                                            <i class="fa fa-save text-white"></i>
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <strong>{{ number_format($lt->trme_qte, 2, ',', ' ') }} Kg</strong>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if (!$transformation_selectionnee->trans_etat || Auth::user()->user_etat == 1)
                                                                    <span class="text-success"><i class="fa fa-unlock"></i> Éditable</span>
                                                                @else
                                                                    <span class="text-muted"><i class="fa fa-lock"></i> Verrouillé</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Aucun
                                                                produit fini associé.</td>
                                                        </tr>
                                                    @endforelse

                                                    @if (count($liaisons_transformer) > 0)
                                                        <tr class="table-warning">
                                                            <td colspan="3" class="text-right"><strong>Volume Total
                                                                    Obtenu :</strong></td>
                                                            <td colspan="2">
                                                                <strong>{{ number_format($totalProduit, 2, ',', ' ') }}
                                                                    Kg</strong>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fa fa-refresh fa-spin fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Sélectionnez une transformation à gauche pour inspecter le détail
                                    des matières et des rendements.</h4>
                            </div>
                        </div>
                    @endif
                </div>
                {{-- ################################################################### --}}
                {{-- ################## FIN La partie Onglets Détails ################## --}}
                {{-- ################################################################### --}}

            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Permet de rester sur l'onglet produit après une modification de quantité
            const urlParams = new URLSearchParams(window.location.search);
            let activeTab = urlParams.get('tab');

            // Force l'affichage de l'onglet produit si on vient d'ajuster une ligne
            if (!activeTab && urlParams.get('trans_id')) {
                activeTab = 'pills-products';
            }

            if (activeTab) {
                const tabLink = document.querySelector(`a[href="#${activeTab}"]`);
                if (tabLink) {
                    // Petit hack JQuery natif de Bootstrap 4 utilisé dans ton template
                    $(tabLink).tab('show');
                }
            }
        });
    </script>
@endsection
