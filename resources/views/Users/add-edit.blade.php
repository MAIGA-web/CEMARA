@extends('layout.header')

@section('contenu')
    <div class="content mt-3">
        <div class="animated fadeIn">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <strong class="card-title">
                                {{ $user->exists ? 'Modifier l\'utilisateur' : 'Ajouter un nouvel utilisateur' }}
                            </strong>
                            <a href="{{ route('Users.index') }}" class="btn btn-sm btn-secondary float-right">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('users.save', $user->id) }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label class="form-control-label">Nom complet</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                        class="form-control @error('name') is-invalid @enderror" required>
                                    @error('name')
                                        <div class="invalid-feed<form action="{{ route('Users.index', $user->id) }}"
                                            method="POST">
                                            @csrfback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Adresse Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                        class="form-control @error('email') is-invalid @enderror" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Rôle / Niveau d'accès</label>
                                    <select name="user_etat" class="form-control">
                                         <option value="4"
                                            {{ old('user_etat', $user->user_etat) == 4 ? 'selected' : '' }}>
                                            Utilisateur Standard (Gardien)
                                        </option>
                                        <option value="3"
                                            {{ old('user_etat', $user->user_etat) == 3 ? 'selected' : '' }}>
                                            Utilisateur Standard (Employé)
                                        </option>
                                        <option value="2"
                                            {{ old('user_etat', $user->user_etat) == 2 ? 'selected' : '' }}>
                                            Administrateur de Ferme
                                        </option>

                                        {{-- Seul le Super Admin peut créer/voir l'option Super Admin --}}
                                        @if (auth()->user()->user_etat == 1)
                                            <option value="1"
                                                {{ old('user_etat', $user->user_etat) == 1 ? 'selected' : '' }}>
                                                Super Administrateur
                                            </option>
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">
                                        Mot de passe {{ $user->exists ? '(Laissez vide pour ne pas modifier)' : '' }}
                                    </label>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        {{ $user->exists ? '' : 'required' }}>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <div class="form-actions form-group">
                                    <button type="submit" class="btn btn-success btn-md">
                                        <i class="fa fa-dot-circle-o"></i>
                                        {{ $user->exists ? 'Mettre à jour' : 'Enregistrer l\'utilisateur' }}
                                    </button>
                                    <button type="reset" class="btn btn-danger btn-md">
                                        <i class="fa fa-ban"></i> Réinitialiser
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
