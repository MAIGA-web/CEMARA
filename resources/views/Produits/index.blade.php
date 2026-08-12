@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">

                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Liste des Produits</strong>
                                <a href="{{ url('/Produits/add-edit') }}" class="btn btn-outline-primary btn-lg  float-right">
                                    Nouveau Produits
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
                                            <th style="width:8px; font-size:10px;">N°</th>
                                            <th>Nom</th>
                                            <th>Type</th>
                                            <th>Stock</th>
                                            <th>Etat</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @foreach ($produit as $produit_value)
                                            @php
                                                echo '<td>' . $i++ . '</td>';
                                            @endphp
                                            <td>{{ $produit_value['pro_nom'] }}</td>
                                            <td>{{ $produit_value['pro_type'] }}</td>
                                            <td>{{ $produit_value['pro_stock'] }}</td>
                                            @if ($produit_value['pro_etat'] == 1)
                                                <td class="text-success">Vendre</td>
                                            @endif

                                            @if ($produit_value['pro_etat'] == 3)
                                                <td class="text-success">vendre</td>
                                            @endif

                                            @if ($produit_value['pro_etat'] == 0)
                                                <td class="text-danger">Non vendre</td>
                                            @endif
                                            @if ($produit_value['pro_etat'] == 2)
                                                <td class="text-danger">Non vendre</td>
                                            @endif

                                            {{-- <td>{{$produit_value['four_etat']}}</td> --}}
                                            <td>
                                                <a href="{{ url('/Produits/add-edit/' . $produit_value['id']) }}"
                                                    class="btn btn-sm btn-primary" {{-- data-url="{{ url('/Fournisseurs/add-edit/'.$produit_value['id']) }}" --}}>
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="{{ url('Produits/delete/' . $produit_value->id) }}"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Supprimer ce Produit de type {{ $produit_value->pro_type }} avec le stock de {{ $produit_value->pro_stock }} ?')">

                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                            </tr>

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

    </div>
@endsection
