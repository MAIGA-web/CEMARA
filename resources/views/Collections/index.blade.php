@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">

            {{-- Notifications --}}
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
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Historique des Ramassages</strong>
                            <a href="{{ url('/Collections?acc=C') }}" class="btn btn-outline-primary btn-sm float-right">
                                <i class="fa fa-plus"></i> Nouveau Ramassage
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($collections as $index => $col)
                                        @php
                                            $isEstSelectionne =
                                                isset($collectionSelectionnee) &&
                                                $collectionSelectionnee->id == $col->id;
                                        @endphp
                                        <tr class="{{ $isEstSelectionne ? 'table-info font-weight-bold' : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ url('/Collections?col_id=' . $col->id) }}" class="text-primary">
                                                    {{ $col->created_at->format('d/m/Y H:i') }}
                                                </a>
                                            </td>
                                            <td>{{ $col->poulailler->poul_nom ?? 'N/A' }}</td>
                                            <td>
                                                @if ($col->col_etat == 1)
                                                    <span class="badge badge-success"><i class="fa fa-lock"></i> Validé
                                                        (Stock à jour)
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fa fa-clock-o"></i> En
                                                        cours</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{-- Visible par tous si non validé, ou réservé à l'Admin (user_etat == 1) si déjà validé --}}
                                                @if ($col->col_etat == 0 || Auth::user()->user_etat == 1)
                                                    <a href="{{ url('/Collections?acc=U&col_id=' . $col->id) }}"
                                                        class="btn btn-sm btn-primary" title="Modifier l'en-tête"><i
                                                            class="fa fa-pencil"></i></a>

                                                    {{-- Le bouton de validation reste masqué si déjà validé --}}
                                                    @if ($col->col_etat == 0)
                                                        <form method="POST" action="{{ url('/Collections') }}"
                                                            style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="emp" value="CV">
                                                            <input type="hidden" name="col_id" value="{{ $col->id }}">
                                                            <button type="submit" name="valider" value="Oui"
                                                                class="btn btn-sm btn-success" title="Valider définitivement"
                                                                onclick="return confirm('Valider le ramassage et injecter le reste au stock ?')"><i
                                                                    class="fa fa-check"></i></button>
                                                        </form>
                                                        @else  <span class="badge badge-success"><i class="fa fa-lock"></i>
                                                        Validé</span>
                                                    @endif

                                                    <form method="POST" action="{{ url('/Collections') }}"
                                                        style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="emp" value="D">
                                                        <input type="hidden" name="col_id" value="{{ $col->id }}">
                                                        <button type="submit" name="valider" value="Oui"
                                                            class="btn btn-sm btn-danger" title="Supprimer"
                                                            onclick="return confirm('Supprimer cette fiche et ses détails ?')"><i
                                                                class="fa fa-trash"></i></button>
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

                <div class="col-md-5">
                    @if ($collectionSelectionnee)
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <strong>Détails du ramassage du :
                                    {{ $collectionSelectionnee->created_at->format('d/m/Y') }}</strong>
                                @if ($collectionSelectionnee->col_etat == 0 || Auth::user()->user_etat == 1)
                                    <button class="btn btn-sm btn-primary float-right" data-toggle="collapse"
                                        data-target="#formCollecter">
                                        <i class="fa fa-plus"></i> Ajouter un lot
                                    </button>
                                @endif
                            </div>

                            <div class="card-body">
                                @if ($collectionSelectionnee->col_etat == 0 || Auth::user()->user_etat == 1)
                                    <div id="formCollecter" class="collapse p-3 mb-3 bg-light border rounded">
                                        @include('Collections.partials.create')
                                    </div>
                                @endif

                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ramassés</th>
                                            <th>Cassés</th>
                                            <th>Consommés</th>
                                            <th>Restes</th>
                                            @if ($collectionSelectionnee->col_etat == 0 || Auth::user()->user_etat == 1)
                                                <th>Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detailsCollecter as $item)
                                            @php
                                                $perdu = $item->qte_consomme + $item->qte_casse;
                                                $reste = $item->qte_ramasse - $perdu;
                                            @endphp
                                            <tr>
                                                <td><strong class="text-primary">{{ $item->qte_ramasse }}</strong></td>
                                                <td><strong class="text-danger">{{ $item->qte_casse }}</strong></td>
                                                <td><strong class="text-warning">{{ $item->qte_consomme }}</strong></td>
                                                <td><strong class="text-success">{{ $reste }}</strong></td>
                                                @if ($collectionSelectionnee->col_etat == 0 || Auth::user()->user_etat == 1)
                                                    <td>
                                                        <a href="{{ url('/Collections?acc=AM&coll_id=' . $item->id . '&col_id=' . $collectionSelectionnee->id) }}"
                                                            class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>

                                                        <form method="POST" action="{{ url('/Collections') }}"
                                                            style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="emp" value="CD">
                                                            <input type="hidden" name="coll_id"
                                                                value="{{ $item->id }}">
                                                            <input type="hidden" name="col_id"
                                                                value="{{ $collectionSelectionnee->id }}">
                                                            <button type="submit" name="valider" value="Oui"
                                                                class="btn btn-link text-danger p-0 border-0"
                                                                onclick="return confirm('Supprimer cette ligne ?')"><i
                                                                    class="fa fa-trash-alt"></i></button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Aucune donnée saisie.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Zone d'affichage pour la modification d'une ligne d'œuf --}}
            @if ($acc === 'AM' && $collecterEnEdition)
                <div class="row">
                    <div class="col-md-7"></div>
                    <div class="col-md-5">
                        @include('Collections.partials.edit_collecter')
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection