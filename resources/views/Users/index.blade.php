@extends('layout.header')

@section('contenu')
    <div class="content mt-3">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">Utilisateurs de : {{ session('fer_nom') }}</strong>
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm float-right">
                    <i class="fa fa-plus"></i> Ajouter un compte
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
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @if ($u->user_etat == 1)
                                        <span class="badge badge-danger">Super Admin</span>
                                    @elseif($u->user_etat == 2)
                                        <span class="badge badge-info">Admin Ferme</span>
                                    @else
                                        <span class="badge badge-secondary">Employé</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('users.save', $u->id) }}" class="btn btn-sm btn-info">
                                        <i class="fa fa-pencil"></i> Modifier
                                    </a>
                                    <a href="{{ route('users.delete', $u->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer .')">
                                        <i class="fa fa-pencil"></i> Suprrimer
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
