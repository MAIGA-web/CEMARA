@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">

                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Liste des Mode</strong>
                            <a href="{{ url('/Modes/add-edit') }}" class="btn btn-outline-primary btn-lg  float-right">
                                Nouveau Mode
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
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($mode as $mode_value)
                                        @php
                                            echo '<td>' . $i++ . '</td>';
                                        @endphp
                                        <td>{{ $mode_value['mod_nom'] }}</td>
                                        <td>
                                            <a href="{{ url('/Modes/add-edit/' . $mode_value['id']) }}"
                                                class="btn btn-primary" {{-- data-url="{{ url('/Fournisseurs/add-edit/'.$mode_value['id']) }}" --}}>
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a href="{{ url('/Modes/delete/' . $mode_value['id']) }}"
                                                class="btn btn-danger" onclick="return confirm('Voulez-vous supprimer ce mode de paiment ?')"><i class="fa fa-trash"></i></a>
                                                
                                        </a>
                                        </td>
                                        </tr>
                                        {{-- Modal pour la suppression --}}
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
