@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">

                {{-- ################################################################### --}}
                {{-- ################## La partie Achat ################################ --}}
                {{-- ################################################################### --}}

                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title text-primary">Gestion des Achats</strong>
                            <a href="{{ route('achat.create') }}" class="btn btn-sm btn-primary float-right">
                                <i class="fa fa-plus"></i> Nouveau Achat
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
                                            <th>Fournisseur</th>
                                            <th>Date</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1; @endphp
                                        @foreach ($achats as $v)
                                            @php
                                                $isEstSelectionne = request('details') == $v->id;
                                            @endphp

                                            <tr class="{{ $isEstSelectionne ? 'table-primary font-weight-bold' : '' }}">
                                                <td>{{ $i++ }}</td>
                                                <td>
                                                    <a href="{{ route('achat.index', ['details' => $v->id]) }}"
                                                        class="btn btn-sm {{ $isEstSelectionne ? 'btn-primary shadow' : 'btn-info' }}">
                                                        @if ($isEstSelectionne)
                                                            <i class="fa fa-arrow-circle-right mr-1"></i>
                                                        @endif
                                                        {{ $v->fournisseur->four_nom ?? '' }}
                                                        {{ $v->fournisseur->four_prenom ?? '' }}
                                                    </a>
                                                </td>
                                                <td>{{ $v->created_at->format('d/m/Y') }}</td>
                                                <td class="text-center">
                                                    @if (!$v->ac_etat || Auth::user()->user_etat == 1)
                                                        {{-- Si l'achat n'est pas validé OU si c'est un Admin (user_etat = 1) --}}
                                                        @if (!$v->ac_etat)
                                                            <a href="{{ route('achats.valider', $v->id) }}"
                                                                class="btn btn-sm btn-success"
                                                                onclick="return confirm('Valider définitivement cet achat ?')"
                                                                title="Valider l'achat">
                                                                <i class="fa fa-check-square-o"></i>
                                                            </a>
                                                        @else<span class="badge badge-success"><i
                                                                    class="fa fa-check"></i> Validé</span>
                                                        @endif

                                                        <a href="{{ route('achat.create', $v->id) }}"
                                                            class="btn btn-sm btn-warning" title="Modifier">
                                                            <i class="fa fa-pencil text-white"></i>
                                                        </a>

                                                        <a href="{{ route('achats.delete', $v->id) }}"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Supprimer ?')" title="Supprimer">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    @else
                                                        {{-- Si l'achat est validé et que l'utilisateur est un simple utilisateur --}}
                                                        <span class="badge badge-success"><i class="fa fa-check"></i>
                                                            Validé</span>
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
                {{-- ################## Fin de La partie Achat ######################### --}}
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

                    @if ($achatSelectionnee)
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <strong class="card-title text-white">
                                    Détails de l'Achat de :
                                    {{ $achatSelectionnee->fournisseur->four_nom ?? '' }}
                                    {{ $achatSelectionnee->fournisseur->four_prenom ?? '' }}
                                </strong>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home"
                                            role="tab" aria-controls="pills-home" aria-selected="true">Info Achat</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile"
                                            role="tab" aria-controls="pills-profile" aria-selected="false">Produits
                                            Achetés
                                            ({{ $produitAchete->count() }})</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-paie-tab" data-toggle="pill" href="#pills-paie"
                                            role="tab" aria-controls="pills-paie" aria-selected="false">Règlements</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="pills-tabContent">
                                    {{-- ################################################################### --}}
                                    {{-- ################## Onglet du Détail de l'Achat ################## --}}
                                    {{-- ################################################################### --}}
                                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                        aria-labelledby="pills-home-tab">
                                        @php
                                            $totalAchat = $produitAchete->sum(fn($p) => $p->act_pu * $p->act_qte);
                                            $sommePayee = \App\Models\Reglement::where(
                                                'ac_id',
                                                $achatSelectionnee->id,
                                            )->sum('re_mnt');
                                            $resteApayer = $totalAchat - $sommePayee;
                                            $modes = \DB::table('modes')->get();
                                        @endphp
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Date de l'Achat :
                                                <span class="badge badge-secondary badge-pill">
                                                    {{ \Carbon\Carbon::parse($achatSelectionnee->created_at)->format('d/m/Y H:i') }}
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                État de la facture :
                                                <span
                                                    class="badge badge-{{ $achatSelectionnee->ac_etat ? 'success' : 'danger' }} badge-pill">
                                                    {{ $achatSelectionnee->ac_etat ? 'Réglée / Validée' : 'En attente de règlement' }}
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Facturé : <strong>{{ number_format($totalAchat, 0, ',', ' ') }}
                                                    F</strong>
                                            </li>
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center text-success">
                                                Total Décaissé : <strong>{{ number_format($sommePayee, 0, ',', ' ') }}
                                                    F</strong>
                                            </li>
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center text-danger">
                                                Reste à payer : <strong>{{ number_format($resteApayer, 0, ',', ' ') }}
                                                    F</strong>
                                            </li>
                                        </ul>
                                        @if ($resteApayer > 0)
                                            <div class="alert alert-warning mt-3">
                                                <i class="fa fa-warning"></i> Attention : Cet achat n'est pas encore
                                                totalement réglé.
                                            </div>
                                        @endif
                                    </div>
                                    {{-- ################################################################### --}}
                                    {{-- ################## FIN Onglet du Détail de l'Achat ################ --}}
                                    {{-- ################################################################### --}}


                                    {{-- ################################################################### --}}
                                    {{-- ################## Onglet des produits en Achat ################### --}}
                                    {{-- ################################################################### --}}
                                    <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                        aria-labelledby="pills-profile-tab">
                                        @if (!$achatSelectionnee->ac_etat || Auth::user()->user_etat == 1)
                                            <h5 class="mb-3 text-primary">Ajouter un produit à cet achat</h5>
                                            <form action="{{ route('acheter.store') }}" method="POST"
                                                class="form-inline mb-4 p-3 bg-light rounded" style="gap: 10px;">
                                                @csrf
                                                <input type="hidden" name="ac_id"
                                                    value="{{ $achatSelectionnee->id }}">

                                                <select name="pro_id" class="form-control form-control-sm" required
                                                    style="flex: 2;">
                                                    <option value="">-- Choisir un produit --</option>
                                                    @foreach ($produits as $p)
                                                        <option value="{{ $p->id }}">
                                                            {{ $p->pro_nom }} ({{ $p->pro_type }}) (Stock:
                                                            {{ $p->pro_stock }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="number" step="0.01" name="act_pu"
                                                    class="form-control form-control-sm" placeholder="Prix Unit." required
                                                    style="flex: 1; max-width: 100px;">
                                                <input type="number" name="act_qte" class="form-control form-control-sm"
                                                    placeholder="Qte" required style="flex: 1; max-width: 80px;">

                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fa fa-plus"></i> Ajouter
                                                </button>
                                            </form>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fa fa-lock"></i> Cet achat est clôturé. Seul un administrateur
                                                peut modifier les articles.
                                            </div>
                                        @endif
                                        <hr>
                                        <h5 class="mb-3">Liste des produits achetés</h5>
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
                                                    @php $i = 1; @endphp
                                                    @forelse($produitAchete as $pv)
                                                        @php
                                                            $subTotal = $pv->act_pu * $pv->act_qte;
                                                            $grandTotal += $subTotal;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $i++ }}</td>
                                                            <td>{{ $pv->produit->pro_nom }} ( {{ $pv->produit->pro_type }}
                                                                )</td>
                                                            <td>{{ number_format($pv->act_pu, 0, ',', ' ') }} F</td>
                                                            <td>{{ $pv->act_qte }}</td>
                                                            <td>{{ number_format($subTotal, 0, ',', ' ') }} F</td>
                                                            <td class="text-center">
                                                                @if (!$achatSelectionnee->ac_etat || Auth::user()->user_etat == 1)
                                                                    {{-- Visible par TOUS si pas validé, OU uniquement si user_etat = 1 après validation --}}
                                                                    <a href="{{ route('acheter.edit', [$pv->id, 'tab' => 'pills-profile']) }}"
                                                                        class="btn btn-sm btn-warning" title="Modifier">
                                                                        <i class="fa fa-edit text-white"></i>
                                                                    </a>

                                                                    <a href="{{ route('acheter.delete', [$pv->id, 'tab' => 'pills-profile']) }}"
                                                                        class="btn btn-sm btn-danger"
                                                                        onclick="return confirm('Retirer ?')"
                                                                        title="Supprimer">
                                                                        <i class="fa fa-trash"></i>
                                                                    </a>
                                                                @else
                                                                    <i class="fa fa-lock text-muted"
                                                                        title="Achat verrouillé"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">Aucun
                                                                produit dans cet achat.</td>
                                                        </tr>
                                                    @endforelse
                                                    @if ($produitAchete->count() > 0)
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
                                    {{-- ################################################################### --}}
                                    {{-- ################## FIN Onglet des produits en Achat ############### --}}
                                    {{-- ################################################################### --}}


                                    {{-- ################################################################### --}}
                                    {{-- ################## Onglet du règlement de l'Achat ################# --}}
                                    {{-- ################################################################### --}}
                                    <div class="tab-pane fade" id="pills-paie" role="tabpanel"
                                        aria-labelledby="pills-paie-tab">
                                        @if ($achatSelectionnee->ac_etat)
                                            <h5 class="mb-3 text-primary">Nouveau Règlement</h5>

                                            @php
                                                $maxAutorise = $totalAchat - $sommePayee;
                                            @endphp
                                            <form action="{{ route('reglement.store') }}" method="POST"
                                                class="form-inline bg-light p-3 rounded mb-4">
                                                @csrf
                                                <input type="hidden" name="ac_id"
                                                    value="{{ $achatSelectionnee->id }}">

                                                <div class="form-group mr-2">
                                                    <label class="sr-only">Montant</label>
                                                    <div class="input-group">
                                                        <input type="number" name="re_mnt" class="form-control"
                                                            placeholder="Montant à régler" max="{{ $maxAutorise }}"
                                                            step="1" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">/
                                                                {{ number_format($maxAutorise, 0, ',', ' ') }} F</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <select name="mod_id" class="form-control mr-2" required>
                                                    <option value="">-- Mode de paiement --</option>
                                                    @foreach ($modes as $m)
                                                        @if ($achatSelectionnee && $m->fer_id == $achatSelectionnee->fer_id)
                                                            <option value="{{ $m->id }}">{{ $m->mod_nom }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>

                                                <button type="submit" class="btn btn-success"
                                                    {{ $maxAutorise <= 0 ? 'disabled' : '' }}>
                                                    <i class="fa fa-money"></i> Régler
                                                </button>
                                            </form>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fa fa-info-circle"></i> Vous devez <strong>valider
                                                    l'Achat</strong> (bouton check à gauche) pour pouvoir enregistrer des
                                                règlements.
                                            </div>
                                        @endif

                                        <h6>Historique des Règlements</h6>
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Montant</th>
                                                    <th>Motif</th>
                                                    <th class="text-center">État</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($historique as $p)
                                                    <tr>
                                                        <td>{{ $p->created_at->format('d/m/Y') }}</td>
                                                        <td>{{ number_format($p->re_mnt, 0, ',', ' ') }} F</td>
                                                        <td>{{ $p->re_motif }}</td>
                                                        <td class="text-center">
                                                            <span
                                                                class="badge badge-{{ $p->re_etat ? 'success' : 'secondary' }}">
                                                                {{ $p->re_etat ? 'Validé' : 'En attente' }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            @if (!$p->re_etat || Auth::user()->user_etat == 1)
                                                                {{-- Visible par TOUS si règlement non validé, OU seulement par user_etat = 1 après validation --}}
                                                                @if (!$p->re_etat)
                                                                    <a href="{{ route('reglement.valider', [$p->id, 'tab' => 'pills-paie']) }}"
                                                                        class="btn btn-xs btn-success"
                                                                        title="Valider le règlement"
                                                                        onclick="return confirm('Voulez-vous valider ce règlement ?')">
                                                                        <i class="fa fa-check"></i>
                                                                    </a>
                                                                @endif

                                                                <a href="{{ route('reglement.edit', [$p->id, 'tab' => 'pills-paie']) }}"
                                                                    class="btn btn-xs btn-warning"
                                                                    title="Modifier le règlement">
                                                                    <i class="fa fa-pencil text-white"></i>
                                                                </a>

                                                                <a href="{{ route('reglement.delete', [$p->id, 'tab' => 'pills-paie']) }}"
                                                                    class="btn btn-xs btn-danger"
                                                                    onclick="return confirm('Supprimer ?')"
                                                                    title="Supprimer">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                            @else
                                                                <i class="fa fa-lock text-muted"
                                                                    title="Règlement verrouillé"></i>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">Aucun règlement
                                                            enregistré.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- ################################################################### --}}
                                    {{-- ################## FIN Onglet du règlement de l'Achat ############# --}}
                                    {{-- ################################################################### --}}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fa fa-file-text-o fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Sélectionnez un Achat à gauche pour en voir et modifier les détails.
                                </h4>
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
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');

            if (tabParam) {
                const targetTab = document.querySelector(`a[href="#${tabParam}"]`);
                if (targetTab) {
                    targetTab.click();
                }
            }
        });
    </script>
@endsection
