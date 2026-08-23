@extends('layout.header')
@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="col-5">
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
                                    <strong>Succès !</strong> {{ session('success_delete') }} <strong
                                        class="text-danger">supprimé</strong>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="card-header">
                            <strong class="card-title">Liste des clients</strong>
                            <a href="{{ url('/Clients/add-edit') }}" class="btn btn-outline-primary btn-lg  float-right">
                                Nouveau Client
                            </a>
                        </div>
                        <div class="card-body">


                            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:8px; font-size:10px;">N°</th>
                                        <th>Nom</th>
                                        <th>Prenom</th>
                                        <th>Adresse</th>
                                        <th>Sexe</th>
                                        <th>Tel</th>
                                        <th>Etat</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($client as $client_value)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $client_value['cl_nom'] }}</td>
                                            <td>{{ $client_value['cl_prenom'] }}</td>
                                            <td>{{ $client_value['cl_adresse'] }}</td>
                                            <td>{{ $client_value['cl_sexe'] }}</td>
                                            <td>{{ $client_value['cl_tel'] }}</td>
                                            @if ($client_value['cl_etat'] == 1)
                                                <td class="text-success">Actif</td>
                                            @else
                                                <td class="text-danger">Inactif</td>
                                            @endif
                                            {{-- <td>{{$client_value['cl_etat']}}</td> --}}
                                            <td>
                                                <a href="{{ url('/Clients/add-edit/' . $client_value['id']) }}"
                                                    class="btn btn-primary" {{-- data-url="{{ url('/Clients/add-edit/'.$client_value['id']) }}" --}}>
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="{{ url('/Clients/delete/' . $client_value['id']) }}"
                                                    class="btn btn-danger"
                                                    onclick="return confirm('Voulez-vous supprimer le client {{ $client_value['cl_prenom'] }} {{ $client_value['cl_nom'] }}')"><i
                                                        class="fa fa-trash"></i>
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
        </div><!-- .animated -->
    </div>
@endsection
