@extends('layout.header')

<div id="right-panel" class="right-panel">
    <div class="content">
        <div class="animated fadeIn">
            
            {{-- Alertes Flash --}}
            @if(session('success_message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Succès !</strong> {{ session('success_message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if(session('error_message'))
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
                            <strong class="card-title">Suivi des Alimentations</strong>
                            <a href="{{ url('/Alimentations/add-edit') }}" class="btn btn-outline-primary btn-sm float-right">
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
                                    @foreach($alimentations as $index => $alm)
                                        <tr class="{{ isset($alimentationSelectionnee) && $alimentationSelectionnee->id == $alm->id ? 'table-info' : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ url('/Alimentations?alm_id=' . $alm->id) }}" class="text-primary">
                                                    {{ $alm->created_at->format('d/m/Y H:i') }}
                                                </a>
                                            </td>
                                            <td>{{ $alm->poulailler->poul_nom ?? 'N/A' }}</td>
                                            <td>
                                                @if($alm->alm_etat == 1)
                                                    <span class="badge badge-success"><i class="fa fa-lock"></i> Validé</span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fa fa-clock-o"></i> En cours</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($alm->alm_etat == 0)
                                                    <a href="{{ url('/Alimentations?acc=M&alm_id=' . $alm->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>
                                                    
                                                    <form method="POST" action="{{ url('/Alimentations') }}" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="emp" value="AV">
                                                        <input type="hidden" name="alm_id" value="{{ $alm->id }}">
                                                        <button type="submit" name="valider" value="Oui" class="btn btn-sm btn-success" onclick="return confirm('Clôturer définitivement cette fiche ?')"><i class="fa fa-check"></i></button>
                                                    </form>

                                                    <form method="POST" action="{{ url('/Alimentations') }}" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="emp" value="D">
                                                        <input type="hidden" name="alm_id" value="{{ $alm->id }}">
                                                        <button type="submit" name="valider" value="Oui" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette fiche et tous ses composants ?')"><i class="fa fa-trash"></i></button>
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
                    @if($alimentationSelectionnee)
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <strong>Composants de la Fiche du : {{ $alimentationSelectionnee->created_at->format('d/m/Y') }}</strong>
                                @if($alimentationSelectionnee->alm_etat == 0)
                                    <button class="btn btn-sm btn-primary float-right" data-toggle="collapse" data-target="#formAlimenter">
                                        <i class="fa fa-plus"></i> Distribuer Aliment
                                    </button>
                                @endif
                            </div>
                            
                            <div class="card-body">
                                {{-- Inclusion du formulaire d'ajout d'aliment partiel --}}
                                @if($alimentationSelectionnee->alm_etat == 0)
                                    <div id="formAlimenter" class="collapse bg-light border rounded">
                                        @include('Alimentations.partials.create')
                                    </div>
                                @endif

                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Aliment</th>
                                            <th>Quantité</th>
                                            @if($alimentationSelectionnee->alm_etat == 0) <th>Actions</th> @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detailsAlimenter as $item)
                                            <tr>
                                                <td>{{ $item->produit->pro_nom }} <small class="text-muted">({{ $item->produit->pro_type }})</small></td>
                                                <td><strong>{{ $item->almt_qte }} kg/sacs</strong></td>
                                                @if($alimentationSelectionnee->alm_etat == 0) <i class="fa fa-pencil"></i>
                                                    <td>
                                                        <a href="{{ url('/Alimentations?acc=AM&almt_id='.$item->id.'&alm_id='.$alimentationSelectionnee->id) }}" class="btn btn-sm btn-primary"> <i class="fa fa-pencil"></i></a>
                                                        
                                                        <form method="POST" action="{{ url('/Alimentations') }}" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="emp" value="AD">
                                                            <input type="hidden" name="almt_id" value="{{ $item->id }}">
                                                            <input type="hidden" name="alm_id" value="{{ $alimentationSelectionnee->id }}">
                                                            <button type="submit" name="valider" value="Oui" class="btn btn-link text-danger p-0 border-0" onclick="return confirm('Retirer cet aliment de la distribution ?')"><i class="fa fa-trash-alt"></i></button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Aucun aliment distribué pour cette session.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Inclusion conditionnelle des formulaires d'édition à partir de votre dossier partiel --}}
            @if($acc === 'AM' && $alimenterEnEdition)
                <div class="row">
                    
                    <div class="col-7">

                        {{-- @include('Alimentations.partials.edit_alimenter') --}}
                    </div>
                         <div class="col-5">

                        @include('Alimentations.partials.edit_alimenter')
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>