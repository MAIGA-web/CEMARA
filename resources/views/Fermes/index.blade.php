@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Liste des Fermes</strong>
                            <a href="{{ route('fermes.save') }}" class="btn btn-outline-primary float-right">
                                <i class="fa fa-plus"></i> Nouvelle Ferme
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

                            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">N°</th>
                                        <th>Logo</th>
                                        <th>Nom</th>
                                        <th>Localisation</th>
                                        <th>Téléphone</th>
                                        <th>État</th>
                                        <th style="width:120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($fermes as $key => $ferme_value)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td class="text-center">
                                                @if ($ferme_value->fer_logo)
                                                    <img src="{{ asset('storage/' . $ferme_value->fer_logo) }}"
                                                        style="width: 40px; height: 40px; border-radius: 5px;">
                                                @else
                                                    <i class="fa fa-university text-muted"></i>
                                                @endif
                                            </td>
                                            <td><strong>{{ $ferme_value->fer_nom }}</strong></td>
                                            <td>{{ $ferme_value->fer_adresse }}</td>
                                            <td>{{ $ferme_value->fer_telephone }}</td>
                                            <td>{{ $ferme_value->fer_email }}</td>
                                            <td>{{ $ferme_value->fer_tel }}</td>
                                            <td>
                                                @if ($ferme_value->fer_etat == 1)
                                                    <span class="badge badge-success">Actif</span>
                                                @else
                                                    <span class="badge badge-danger">Inactif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('fermes.save', $ferme_value->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fa fa-pencil"></i>
                                                </a>

                                                <button class="btn btn-sm btn-danger" data-toggle="modal"
                                                    data-target="#deleteModal{{ $ferme_value->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>

                                            </td>
                                        </tr>

                                        {{-- Modal spécifique pour chaque ferme --}}
                                        <div class="modal fade" id="deleteModal{{ $ferme_value->id }}" tabindex="-1"
                                            role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmation de suppression</h5>
                                                        <button type="button" class="close"
                                                            data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Voulez-vous vraiment supprimer la ferme
                                                            <strong>{{ $ferme_value->fer_nom }}</strong> ?
                                                        </p>
                                                        <small class="text-danger">Attention : Toutes les données liées
                                                            seront supprimées.</small>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Annuler</button>
                                                        <a href="{{ url('/Fermes/delete/' . $ferme_value->id) }}"
                                                            class="btn btn-danger">Confirmer la suppression</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
