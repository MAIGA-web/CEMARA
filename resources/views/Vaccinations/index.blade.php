@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Liste des Vaccinations</strong>
                            <a href="{{ url('/Vaccinations/add-edit') }}" class="btn btn-outline-primary btn-lg float-right">
                                Nouvelle Vaccination
                            </a>
                        </div>
                        <div class="card-body">
                            {{-- Notifications Flash --}}
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
                                    <strong>Échec !</strong> {{ session('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:8px; font-size:10px;">N°</th>
                                        <th>Date Vaccination</th>
                                        <th>Type vaccin</th>
                                        <th>Quantité vaccin</th>
                                        <th>Vétérinaire</th>
                                        <th>Poulailler</th>
                                        <th>État</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vaccinations as $index => $vac)
                                        @php
                                            // Vérification si la vaccination est validée (gestion booléen PostgreSQL 't'/true/1)
                                            $isValide = ($vac->vac_etat === 't' || $vac->vac_etat == 1 || $vac->vac_etat === true);
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($vac->created_at)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $vac->produit->pro_nom ?? 'N/A' }}</td>
                                            <td>{{ $vac->vac_qte }} Flacons</td>
                                            <td>{{ trim(($vac->veterinaire->vtr_nom ?? '') . ' ' . ($vac->veterinaire->vtr_prenom ?? '')) ?: 'N/A' }}</td>
                                            <td>{{ $vac->poulailler->poul_nom ?? 'N/A' }}</td>
                                            <td>
                                                {{-- Affichage de l'état textuel --}}
                                                @if ($isValide)
                                                    <span class="badge badge-success"><i class="fa fa-check-circle"></i> Validé</span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fa fa-clock-o"></i> En attente</span>
                                                @endif
                                            </td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                {{-- Visible pour TOUS si non validé, OU uniquement pour l'ADMIN (user_etat == 1) si validé --}}
                                                @if (!$isValide || Auth::user()->user_etat == 1)

                                                    {{-- Bouton de validation rapide (masqué si déjà validé) --}}
                                                    @if (!$isValide)
                                                        <form method="POST" action="{{ url('/Vaccinations') }}" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="emp" value="V">
                                                            <input type="hidden" name="vac_id" value="{{ $vac->id }}">
                                                            <button type="submit" name="valider" value="Oui"
                                                                class="btn btn-sm btn-success" title="Valider définitivement"
                                                                onclick="return confirm('Valider cette vaccination et déduire le stock définitif ?')">
                                                                <i class="fa fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <!-- Bouton Modifier -->
                                                    <a href="{{ url('/Vaccinations/add-edit/' . $vac->id) }}"
                                                        class="btn btn-sm btn-warning" title="Modifier">
                                                        <i class="fa fa-pencil text-white"></i>
                                                    </a>

                                                    <!-- Bouton Supprimer -->
                                                    <form method="POST" action="{{ url('/Vaccinations') }}"
                                                        style="display:inline;"
                                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vaccination ?');">
                                                        @csrf
                                                        <input type="hidden" name="emp" value="D">
                                                        <input type="hidden" name="vac_id" value="{{ $vac->id }}">
                                                        <button type="submit" name="valider" value="Oui"
                                                            class="btn btn-sm btn-danger" title="Supprimer">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>

                                                @else
                                                    {{-- Si validé ET utilisateur non-admin --}}
                                                    <i class="fa fa-lock text-muted" title="Vaccination verrouillée"> Verrouillée</i>
                                                @endif
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