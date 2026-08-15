@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">

            <div class="row">
                <div class="col-md-7">
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
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Suivi des Pertes / Casses</strong>
                            <a href="{{ url('/Pertes?acc=C') }}" class="btn btn-outline-primary btn-sm float-right">
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pertes as $index => $per)
                                        <tr
                                            class="{{ isset($perteSelectionnee) && $perteSelectionnee->id == $per->id ? 'table-info' : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ url('/Pertes?per_id=' . $per->id) }}"
                                                    class="font-weight-bold text-primary">
                                                    {{ $per->created_at->format('d/m/Y H:i') }}
                                                </a>
                                            </td>
                                            <td>{{ $per->poulailler->poul_nom ?? 'N/A' }}</td>
                                            <td>
                                                @if ($per->per_etat == 1)
                                                    <span class="badge badge-success"><i class="fa fa-lock"></i>
                                                        Validé</span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fa fa-clock-o"></i> En
                                                        cours</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{-- Visible avant validation pour tous OU après validation si user_etat = 1 --}}
                                                @if ($per->per_etat == 0 || Auth::user()->user_etat == 1)
                                                    <a href="{{ url('/Pertes?acc=M&per_id=' . $per->id) }}"
                                                        class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>

                                                    {{-- Le bouton de validation reste masqué si la fiche est déjà validée --}}
                                                    @if ($per->per_etat == 0)
                                                        <form method="POST" action="{{ url('/Pertes') }}"
                                                            style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="emp" value="PV">
                                                            <input type="hidden" name="per_id" value="{{ $per->id }}">
                                                            <button type="submit" name="valider" value="Oui"
                                                                class="btn btn-sm btn-success"
                                                                onclick="return confirm('Valider et déduire définitivement les stocks ?')"><i
                                                                    class="fa fa-check"></i></button>
                                                        </form>
                                                    @endif

                                                    <form method="POST" action="{{ url('/Pertes') }}"
                                                        style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="emp" value="D">
                                                        <input type="hidden" name="per_id" value="{{ $per->id }}">
                                                        <button type="submit" name="valider" value="Oui"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Supprimer cette fiche ?')"><i
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
                    @if ($perteSelectionnee)
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <strong>Produits de la Fiche du :
                                    {{ $perteSelectionnee->created_at->format('d/m/Y') }}</strong>
                                @if ($perteSelectionnee->per_etat == 0 || Auth::user()->user_etat == 1)
                                    <button class="btn btn-sm btn-primary float-right" data-toggle="collapse"
                                        data-target="#formPerdre">
                                        <i class="fa fa-plus"></i> Déclarer Perte
                                    </button>
                                @endif
                            </div>

                            <div class="card-body">
                                @if ($perteSelectionnee->per_etat == 0 || Auth::user()->user_etat == 1)
                                    <div id="formPerdre" class="collapse p-3 mb-3 bg-light border rounded">
                                        @include('Pertes.partials.create')
                                    </div>
                                @endif

                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Produit</th>
                                            <th>Qté</th>
                                            <th>Motif</th>
                                            @if ($perteSelectionnee->per_etat == 0 || Auth::user()->user_etat == 1)
                                                <th>Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detailsPerdre as $item)
                                            <tr>
                                                <td>{{ $item->produit->pro_nom }}</td>
                                                <td><strong class="text-danger">{{ $item->perd_qte }}</strong></td>
                                                <td><strong>{{ $item->motif ?? 'Non spécifié' }}</strong></td>
                                                @if ($perteSelectionnee->per_etat == 0 || Auth::user()->user_etat == 1)
                                                    <td>
                                                        <a href="{{ url('/Pertes?acc=AM&perd_id=' . $item->id . '&per_id=' . $perteSelectionnee->id) }}"
                                                            class=" btn btn-primary mr-2"><i class="fa fa-edit"></i></a>

                                                        <form method="POST" action="{{ url('/Pertes') }}"
                                                            style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="emp" value="PD">
                                                            <input type="hidden" name="perd_id"
                                                                value="{{ $item->id }}">
                                                            <input type="hidden" name="per_id"
                                                                value="{{ $perteSelectionnee->id }}">
                                                            <button type="submit" name="valider" value="Oui"
                                                                class="btn btn-link text-danger p-0 border-0"
                                                                onclick="return confirm('Retirer ce produit ?')"><i
                                                                    class="fa fa-trash-alt"></i></button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Aucune perte enregistrée
                                                    sur cette fiche.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if ($acc === 'AM' && $perdreEnEdition)
                <div class="row">
                    <div class="col-7">
                        {{-- @include('Pertes.partials.edit_perdre') --}}
                    </div>
                    <div class="col-5">
                        @include('Pertes.partials.edit_perdre')
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection