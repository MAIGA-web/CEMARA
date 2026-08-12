@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Liste des Matières</strong>
                            <a href="{{ url('/Matieres/add-edit') }}" class="btn btn-outline-primary btn-lg float-right">
                                Nouvelle Matière
                            </a>
                        </div>
                        <div class="card-body">
                            @if (session('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Succès !</strong> {{ session('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @if (session('success_delete'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Succès !</strong> {{ session('success_delete') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">N°</th>
                                        <th>Nom</th>
                                        <th>Type</th>
                                        <th>Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($matieres as $key => $matiere_value)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $matiere_value->ma_nom }}</td>
                                            <td>{{ $matiere_value->ma_type }}</td>
                                            <td>{{ $matiere_value->ma_stock }}</td>
                                            <td>
                                                <a href="{{ url('/Matieres/add-edit/' . $matiere_value->id) }}"
                                                    class="btn btn-primary">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <!-- ID Dynamique pour cibler le bon modal -->
                                                <a href="{{ url('/Matieres/delete/' . $matiere_value->id) }}"
                                                    class="btn btn-danger"
                                                    onclick="return confirm('Voulez-vous supprimer ce matiere ?')"><i
                                                        class="fa fa-trash"></i></a>

                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Modal de suppression unique pour chaque ligne -->
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
@endsection;
