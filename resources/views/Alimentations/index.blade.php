@extends('layout.header')
@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            {{-- Alertes Flash --}}
            @if (session('success_message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Succès !</strong> {{ session('success_message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (session('error_message'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreur !</strong> {{ session('error_message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                {{-- ################################################################### --}}
                {{-- ################## Partie Gauche : Liste Alimentations ############ --}}
                {{-- ################################################################### --}}
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Suivi des Alimentations</strong>
                            <a href="{{ url('/Alimentations/add-edit') }}"
                                class="btn btn-outline-primary btn-sm float-right">
                                <i class="fa fa-plus"></i> Nouvelle Fiche
                            </a>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered bootstrap-data-table">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Date / Heure</th>
                                        <th>Poulailler</th>
                                        <th>État</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alimentations as $index => $alm)
                                        <tr
                                            class="{{ isset($alimentationSelectionnee) && $alimentationSelectionnee->id == $alm->id ? 'table-info' : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ url('/Alimentations?alm_id=' . $alm->id) }}"
                                                    class="text-primary font-weight-bold">
                                                    {{ $alm->created_at->format('d/m/Y H:i') }}
                                                </a>
                                            </td>
                                            <td>{{ $alm->poulailler->poul_nom ?? 'N/A' }}</td>
                                            <td>
                                                @if ($alm->alm_etat == 1)
                                                    <span class="badge badge-success"><i class="fa fa-lock"></i>
                                                        Validé</span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fa fa-clock-o"></i> En
                                                        cours</span>
                                                @endif
                                            </td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                {{-- Accessible à TOUS si non validé, OU uniquement à l'ADMIN (user_etat == 1) si validé --}}
                                                @if ($alm->alm_etat == 0 || Auth::user()->user_etat == 1)
                                                    {{-- Bouton Valider (Masqué si déjà validé) --}}
                                                    @if ($alm->alm_etat == 0)
                                                        <form method="POST" action="{{ url('/Alimentations') }}"
                                                            style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="emp" value="AV">
                                                            <input type="hidden" name="alm_id"
                                                                value="{{ $alm->id }}">
                                                            <button type="submit" name="valider" value="Oui"
                                                                class="btn btn-sm btn-success" title="Clôturer la fiche"
                                                                onclick="return confirm('Clôturer définitivement cette fiche ?')">
                                                                <i class="fa fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    {{-- Bouton Modifier --}}
                                                    <a href="{{ url('/Alimentations?acc=M&alm_id=' . $alm->id) }}"
                                                        class="btn btn-sm btn-primary" title="Modifier la fiche">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>

                                                    {{-- Bouton Supprimer --}}
                                                    <form method="POST" action="{{ url('/Alimentations') }}"
                                                        style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="emp" value="D">
                                                        <input type="hidden" name="alm_id" value="{{ $alm->id }}">
                                                        <button type="submit" name="valider" value="Oui"
                                                            class="btn btn-sm btn-danger" title="Supprimer la fiche"
                                                            onclick="return confirm('Supprimer cette fiche et tous ses composants ?')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <i class="fa fa-lock text-muted" title="Fiche clôturée"> Verrouillée</i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ################################################################### --}}
                {{-- ################## Partie Droite : Composants & Formulaires ###### --}}
                {{-- ################################################################### --}}
                <div class="col-md-5">
                    @if ($alimentationSelectionnee)
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <strong>Composants du :
                                    {{ $alimentationSelectionnee->created_at->format('d/m/Y') }}</strong>

                                {{-- Bouton Distribuer : visible avant validation pour tous, ou après validation pour l'Admin --}}
                                @if ($alimentationSelectionnee->alm_etat == 0 || Auth::user()->user_etat == 1)
                                    <button class="btn btn-sm btn-primary float-right" data-toggle="collapse"
                                        data-target="#formAlimenter">
                                        <i class="fa fa-plus"></i> Distribuer Aliment
                                    </button>
                                @endif
                            </div>

                            <div class="card-body">
                                {{-- Message d'avertissement si modification admin après clôture --}}
                                @if ($alimentationSelectionnee->alm_etat == 1 && Auth::user()->user_etat == 1)
                                    <div class="alert alert-warning py-2 mb-3">
                                        <i class="fa fa-exclamation-triangle"></i> Mode Admin : Vous modifiez une
                                        distribution déjà clôturée.
                                    </div>
                                @endif

                                {{-- Inclusion du formulaire d'ajout d'aliment partiel --}}
                                @if ($alimentationSelectionnee->alm_etat == 0 || Auth::user()->user_etat == 1)
                                    <div id="formAlimenter" class="collapse bg-light border rounded mb-3">
                                        @include('Alimentations.partials.create')
                                    </div>
                                @endif

                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Aliment</th>
                                            <th>Quantité</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detailsAlimenter as $item)
                                            <tr>
                                                <td>
                                                    {{ $item->produit->pro_nom }}
                                                    <small class="text-muted">({{ $item->produit->pro_type }})</small>
                                                </td>
                                                <td><strong>{{ $item->almt_qte }} kg/sacs</strong></td>
                                                <td class="text-center" style="white-space: nowrap;">
                                                    @if ($alimentationSelectionnee->alm_etat == 0 || Auth::user()->user_etat == 1)
                                                        {{-- Bouton Modifier un aliment de la fiche --}}
                                                        <a href="{{ url('/Alimentations?acc=AM&almt_id=' . $item->id . '&alm_id=' . $alimentationSelectionnee->id) }}"
                                                            class="btn btn-sm btn-primary" title="Modifier cet aliment">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>

                                                        {{-- Bouton Supprimer un aliment de la fiche --}}
                                                        <form method="POST" action="{{ url('/Alimentations') }}"
                                                            style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="emp" value="AD">
                                                            <input type="hidden" name="almt_id"
                                                                value="{{ $item->id }}">
                                                            <input type="hidden" name="alm_id"
                                                                value="{{ $alimentationSelectionnee->id }}">
                                                            <button type="submit" name="valider" value="Oui"
                                                                class="btn btn-sm btn-danger ml-1"
                                                                title="Retirer cet aliment"
                                                                onclick="return confirm('Retirer cet aliment de la distribution ?')">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <i class="fa fa-lock text-muted" title="Composant verrouillé"></i>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">Aucun aliment
                                                    distribué pour cette session.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Inclusion conditionnelle du formulaire d'édition partiel --}}
            @if ($acc === 'AM' && $alimenterEnEdition)
                <div class="row">
                    <div class="col-7"></div>
                    <div class="col-5">
                        @include('Alimentations.partials.edit_alimenter')
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
