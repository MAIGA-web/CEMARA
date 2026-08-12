@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">


                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Liste des Poulaillers</strong>
                            <a href="{{ url('/Poulaillers/add-edit') }}" class="btn btn-outline-primary btn-lg  float-right">
                                Nouveau Poulaillers
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
                                    <strong>Succès !</strong> {{ session('success_delete') }} <strong
                                        class="text-danger">supprimé</strong>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:8px; font-size:10px;">N°</th>
                                        <th>Nom</th>
                                        <th>Capacité</th>
                                        <th>Emplacement</th>
                                        <th>Etat</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($poulailler as $poulailler_value)
                                        @php
                                            echo '<td>' . $i++ . '</td>';
                                        @endphp
                                        <td>{{ $poulailler_value['poul_nom'] }}</td>
                                        <td>{{ $poulailler_value['poul_capa'] }}</td>
                                        <td>{{ $poulailler_value['poul_empl'] }}</td>
                                        @if ($poulailler_value['poul_etat'] == 0)
                                            <td class="text-success">En service</td>
                                        @elseif ($poulailler_value['poul_etat'] == 1)
                                            <td class="text-primary">Non réparation</td>
                                        @else
                                            <td class="text-danger">Hors service</td>
                                        @endif
                                        {{-- <td>{{$poulailler_value['four_etat']}}</td> --}}
                                        <td>
                                            <a href="{{ url('/Poulaillers/add-edit/' . $poulailler_value['id']) }}"
                                                class="btn btn-primary" {{-- data-url="{{ url('/Fournisseurs/add-edit/'.$poulailler_value['id']) }}" --}}>
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a href="{{ url('/Poulaillers/delete/' . $poulailler_value['id']) }}"
                                                            class="btn btn-danger" onclick="return confirm('Voulez-vous supprimer le poulailer {{$poulailler_value['poul_nom']}}?')"><i class="fa fa-trash"></i></a>
                                                
                                        </a>
                                        </td>
                                        </tr>
                                        {{-- Modal pour la suppression --}}
                                        <div class="modal fade" id="mediumModal" tabindex="-1" role="dialog"
                                            aria-labelledby="mediumModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="mediumModalLabel">Medium Modal</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-danger font-3xl">
                                                            Voulez-vou suprimer ce produits
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Non</button>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Fin modal pour la suppression --}}
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
