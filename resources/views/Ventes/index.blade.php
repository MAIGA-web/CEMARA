@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">

                {{-- ################################################################### --}}
                {{-- ################## La partie vente ################################ --}}
                {{-- ################################################################### --}}
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title text-primary">Gestion des Ventes</strong>
                            <a href="{{ route('ventes.create') }}" class="btn btn-sm btn-primary float-right">
                                <i class="fa fa-plus"></i> Nouvelle Vente
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
                                            <th>Client</th>
                                            <th>Date</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ventes as $key => $v)
                                            @php
                                                $isEstSelectionne = request('details') == $v->id;
                                            @endphp

                                            <tr class="{{ $isEstSelectionne ? 'table-primary font-weight-bold' : '' }}">
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <span class="{{ $isEstSelectionne ? 'text-primary' : '' }}">
                                                        @if ($isEstSelectionne)
                                                            <i class="fa fa-arrow-circle-right text-primary mr-1"></i>
                                                        @endif
                                                        <a href="{{ route('ventes.index', ['details' => $v->id]) }}"
                                                            class="btn btn-sm {{ $isEstSelectionne ? 'btn-primary shhttp://127.0.0.1:8000adow' : 'btn-info' }}"
                                                            title="Voir les détails">
                                                            {{ $v->client->cl_prenom }} {{ $v->client->cl_nom }}
                                                        </a>
                                                    </span>
                                                </td>
                                                <td>{{ $v->created_at->format('d/m/Y') }}</td>
                                                <td class="text-center">
                                                    @if ($v->vte_etat)
                                                        <span class="badge badge-success">
                                                            <i class="fa fa-check"></i> Validée
                                                        </span>
                                                    @else
                                                        <a href="{{ route('ventes.valider', $v->id) }}"
                                                            class="btn btn-sm btn-success"
                                                            onclick="return confirm('Valider définitivement cette vente ?')"
                                                            title="Valider la vente">
                                                            <i class="fa fa-check-square-o"></i>
                                                        </a>

                                                        <a href="{{ route('ventes.create', $v->id) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fa fa-pencil text-white"></i>
                                                        </a>

                                                        <a href="{{ route('ventes.delete', $v->id) }}"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Supprimer ?')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Le bouton de détails passe en bleu foncé (btn-primary) s'il est sélectionné --}}
                                                    {{-- <a href="{{ route('ventes.index', ['details' => $v->id]) }}"
                                                        class="btn btn-sm {{ $isEstSelectionne ? 'btn-primary shadow' : 'btn-info' }}"
                                                        title="Voir les détails"> --}}
                                                    {{-- <i class="fa {{ $isEstSelectionne ? 'fa-eye' : 'fa-arrow-right' }}"></i> --}}
                                                    </a>
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
                {{-- ################## Fin de La partie vente ######################### --}}
                {{-- ################################################################### --}}


                {{-- ################################################################### --}}
                {{-- ################## La partie onglets ############################## --}}
                {{-- ################################################################### --}}
                <div class="col-md-7">
                    @if (session('error_message'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error_message') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if ($venteSelectionnee)
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <strong class="card-title text-white">
                                    Détails de la vente de : {{ $venteSelectionnee->client->cl_nom }}
                                    {{ $venteSelectionnee->client->cl_prenom }}
                                </strong>
                            </div>
                            <div class="card-body">
                                @php
                                    // Calculs financiers globaux de la vente
                                    $totalVente = $produitsVendus->sum(fn($p) => $p->vdr_pu * $p->vdr_qte);
                                    $sommePayee = \App\Models\Paiement::where('vte_id', $venteSelectionnee->id)->sum(
                                        'pa_payer',
                                    );
                                    $resteApayer = $totalVente - $sommePayee;
                                    $modes = \DB::table('modes')->get();
                                @endphp
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home"
                                            role="tab" aria-controls="pills-home" aria-selected="true">Info Vente</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile"
                                            role="tab" aria-controls="pills-profile" aria-selected="false">Produits
                                            Vendus ({{ $produitsVendus->count() }})</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-paie-tab" data-toggle="pill" href="#pills-paie"
                                            role="tab" aria-controls="pills-paie" aria-selected="false">Paiements</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="pills-tabContent">

                                    {{-- Onglet 1 : Détail de la vente --}}
                                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                        aria-labelledby="pills-home-tab">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Date de la vente :
                                                <span class="badge badge-secondary badge-pill">
                                                    {{ $venteSelectionnee->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                État de la facture :
                                                <span
                                                    class="badge badge-{{ $venteSelectionnee->vte_etat ? 'success' : 'danger' }} badge-pill">
                                                    {{ $venteSelectionnee->vte_etat ? 'Réglée / Clôturée' : 'En attente de validation' }}
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Facturé : <strong>{{ number_format($totalVente, 0, ',', ' ') }}
                                                    F</strong>
                                            </li>
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center text-success">
                                                Total Encaissé : <strong>{{ number_format($sommePayee, 0, ',', ' ') }}
                                                    F</strong>
                                            </li>
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center text-danger">
                                                Reste à recouvrer : <strong>{{ number_format($resteApayer, 0, ',', ' ') }}
                                                    F</strong>
                                            </li>
                                        </ul>
                                        @if ($resteApayer > 0)
                                            <div class="alert alert-warning mt-3">
                                                <i class="fa fa-warning"></i> Attention : Cette vente n'est pas encore
                                                totalement réglée.
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Onglet 2 : Produits en vente --}}
                                    <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                        aria-labelledby="pills-profile-tab">
                                        @if (!$venteSelectionnee->vte_etat)
                                            <h5 class="mb-3 text-primary">Ajouter un produit à cette vente</h5>
                                            <form action="{{ route('vendre.store') }}" method="POST"
                                                class="form-inline mb-4 p-3 bg-light rounded" style="gap: 10px;">
                                                @csrf
                                                <input type="hidden" name="vte_id"
                                                    value="{{ $venteSelectionnee->id }}">

                                                <select name="pro_id" class="form-control form-control-sm" required
                                                    style="flex: 2;">
                                                    <option value="">-- Choisir un produit --</option>
                                                    @foreach ($produits as $p)
                                                        @if ($p->pro_etat == 1)
                                                            <option value="{{ $p->id }}">{{ $p->pro_nom }}
                                                                ({{ $p->pro_type }})
                                                                (Stock: {{ $p->pro_stock }})
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <input type="number" step="0.01" name="vdr_pu"
                                                    class="form-control form-control-sm" placeholder="Prix Unit." required
                                                    style="flex: 1; max-width: 100px;">
                                                <input type="number" name="vdr_qte" class="form-control form-control-sm"
                                                    placeholder="Qte" required style="flex: 1; max-width: 80px;">

                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fa fa-plus"></i> Ajouter
                                                </button>
                                            </form>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fa fa-lock"></i> Cette vente est clôturée. Impossible d'ajouter
                                                ou retirer des produits.
                                            </div>
                                        @endif
                                        <hr>
                                        <h5 class="mb-3">Liste des produits facturés</h5>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>N°</th>
                                                        <th>Produit</th>
                                                        <th>P.U</th>
                                                        <th>Qte</th>
                                                        <th>Total</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $grandTotal = 0; @endphp
                                                    @forelse($produitsVendus as $key => $pv)
                                                        @php
                                                            $subTotal = $pv->vdr_pu * $pv->vdr_qte;
                                                            $grandTotal += $subTotal;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $pv->produit->pro_nom }} ( {{ $pv->produit->pro_type }}
                                                                )</td>
                                                            <td>{{ number_format($pv->vdr_pu, 0, ',', ' ') }} F</td>
                                                            <td>{{ $pv->vdr_qte }}</td>
                                                            <td>{{ number_format($subTotal, 0, ',', ' ') }} F</td>
                                                            {{-- Correction ici : affiche le total de la ligne --}}
                                                            <td class="text-center">
                                                                @if (!$venteSelectionnee->vte_etat)
                                                                    <a href="{{ route('vendre.edit', [$pv->id, 'tab' => 'pills-profile']) }}"
                                                                        class="btn btn-sm btn-warning">
                                                                        <i class="fa fa-edit text-white"></i>
                                                                    </a>
                                                                    <a href="{{ route('vendre.delete', [$pv->id, 'tab' => 'pills-profile']) }}"
                                                                        class="btn btn-sm btn-danger"
                                                                        onclick="return confirm('Retirer ?')">
                                                                        <i class="fa fa-trash"></i>
                                                                    </a>
                                                                @else
                                                                    <i class="fa fa-lock text-muted"
                                                                        title="Ligne verrouillée"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">Aucun
                                                                produit dans cette vente.</td>
                                                        </tr>
                                                    @endforelse
                                                    @if ($produitsVendus->count() > 0)
                                                        <tr class="table-warning">
                                                            <td colspan="4" class="text-right"><strong>TOTAL GÉNÉRAL
                                                                    :</strong></td>
                                                            <td colspan="2">
                                                                <strong>{{ number_format($grandTotal, 0, ',', ' ') }}
                                                                    F</strong>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Onglet 3 : Paiement de la vente --}}
                                    <div class="tab-pane fade" id="pills-paie" role="tabpanel"
                                        aria-labelledby="pills-paie-tab">
                                        @if ($venteSelectionnee->vte_etat)
                                            @php $maxAutorise = $totalVente - $sommePayee; @endphp

                                            <form action="{{ route('paiement.store') }}" method="POST"
                                                class="form-inline bg-light p-3 rounded mb-4">
                                                @csrf
                                                <input type="hidden" name="vte_id"
                                                    value="{{ $venteSelectionnee->id }}">

                                                <div class="form-group mr-2">
                                                    <div class="input-group">
                                                        <input type="number" name="pa_payer" class="form-control"
                                                            placeholder="Montant" max="{{ $maxAutorise }}"
                                                            step="1" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">/
                                                                {{ number_format($maxAutorise, 0, ',', ' ') }} F</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <select name="mod_id" class="form-control mr-2" required>
                                                    <option value="">-- Mode --</option>
                                                    @foreach ($moes as $m)
                                                        <option value="{{ $m->id }}">{{ $m->mod_nom }}</option>
                                                    @endforeach
                                                </select>

                                                <button type="submit" class="btn btn-success"
                                                    {{ $maxAutorise <= 0 ? 'disabled' : '' }}>
                                                    <i class="fa fa-money"></i> Encaisser
                                                </button>
                                            </form>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fa fa-info-circle"></i> Vous devez <strong>valider la
                                                    vente</strong> (bouton vert à gauche) pour pouvoir enregistrer des
                                                paiements.
                                            </div>
                                        @endif

                                        <h6>Historique des paiements
                                            <a href="{{ route('paiement.recu', $venteSelectionnee->id) }}"
                                                target="_blank" class="btn btn-xs btn-secondary float-right mb-2"
                                                title="Imprimer le reçu">
                                                <i class="fa fa-print"></i> Imprimer le reçu
                                            </a>
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Montant</th>
                                                        <th class="text-center">État</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($historique as $p)
                                                        <tr>
                                                            <td>{{ $p->created_at->format('d/m/Y') }}</td>
                                                            <td>{{ number_format($p->pa_payer, 0, ',', ' ') }} F</td>
                                                            <td class="text-center">
                                                                <span
                                                                    class="badge badge-{{ $p->pa_etat ? 'success' : 'secondary' }}">
                                                                    {{ $p->pa_etat ? 'Validé' : 'En attente' }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                @if (!$p->pa_etat)
                                                                    <a href="{{ route('paiement.valider', [$p->id, 'tab' => 'pills-paie']) }}"
                                                                        class="btn btn-xs btn-success"
                                                                        title="Valider le paiement"
                                                                        onclick="return confirm('Valider ce versement ?')">
                                                                        <i class="fa fa-check"></i>
                                                                    </a>
                                                                    <a href="{{ route('paiement.edit', [$p->id, 'tab' => 'pills-paie']) }}"
                                                                        class="btn btn-xs btn-warning">
                                                                        <i class="fa fa-pencil text-white"></i>
                                                                    </a>
                                                                    <a href="{{ route('paiement.delete', [$p->id, 'tab' => 'pills-paie']) }}"
                                                                        class="btn btn-xs btn-danger"
                                                                        onclick="return confirm('Supprimer ?')">
                                                                        <i class="fa fa-trash"></i>
                                                                    </a>
                                                                @else
                                                                    <i class="fa fa-lock text-muted"
                                                                        title="Paiement verrouillé"></i>
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
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fa fa-file-text-o fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Sélectionnez une vente à gauche pour en voir et modifier les
                                    détails.</h4>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Lecture automatique du paramètre 'tab' transmis par la redirection des contrôleurs
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
