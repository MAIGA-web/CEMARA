@extends('layout.header')
@section('contenu')
    <div class="content">
        <div class="row">
            <div class="col-12 pb-2">
                <div class="page-header">
                    <div class="page-title">
                        <h2 class="text-dark font-weight-bold p-3" style="font-size: 26px;">
                            Tableau de bord
                            {{-- ({{ session('fer_nom', auth()->user()->ferme->fer_nom ?? 'Aucune ferme sélectionnée') }}) --}}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="animated fadeIn">

            @if (isset($produitsAlerte) && $produitsAlerte->isNotEmpty())
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-dismissible fade show font-weight-bold shadow-sm"
                            role="alert" style="background-color: #ff4a68; color: white; border: none;">
                            <i class="fa fa-exclamation-triangle"></i> <strong>Alerte Stock</strong> <br>
                            @foreach ($produitsAlerte as $prod)
                                <span style="font-size: 14px; padding-left: 15px; display: inline-block;">•
                                    {{ $prod->pro_nom }} ( {{ $prod->pro_type }} ) : {{ $prod->pro_stock }} en stock</span><br>
                            @endforeach
                            <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <h5 class="text-secondary font-weight-bold mb-4">Bonjour {{ auth()->user()->name }}!!!</h5>

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <strong class="text-dark font-weight-bold">Total Achats</strong>
                            <hr class="my-2">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-between py-1 text-primary"><span>• Par jour :</span>
                                    <strong>{{ number_format($achatsJour ?? 0, 0, ',', ' ') }} FCFA</strong></li>
                                <li class="d-flex justify-between py-1 text-danger"><span>• Par semaine :</span>
                                    <strong>{{ number_format($achatsSemaine ?? 0, 0, ',', ' ') }} FCFA</strong></li>
                                <li class="d-flex justify-between py-1 text-dark"><span>• Par mois :</span>
                                    <strong>{{ number_format($achatsMois ?? 0, 0, ',', ' ') }} FCFA</strong></li>
                                <li class="d-flex justify-between py-1 text-info"><span>• Par ans :</span>
                                    <strong>{{ number_format($achatsAn ?? 0, 0, ',', ' ') }} FCFA</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <strong class="text-dark font-weight-bold">Total Ventes</strong>
                            <hr class="my-2">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-between py-1 text-primary"><span>• Par jour :</span>
                                    <strong>{{ number_format($ventesJour ?? 0, 0, ',', ' ') }} FCFA</strong></li>
                                <li class="d-flex justify-between py-1 text-danger"><span>• Par semaine :</span>
                                    <strong>{{ number_format($ventesSemaine ?? 0, 0, ',', ' ') }} FCFA</strong></li>
                                <li class="d-flex justify-between py-1 text-dark"><span>• Par mois :</span>
                                    <strong>{{ number_format($ventesMois ?? 0, 0, ',', ' ') }} FCFA</strong></li>
                                <li class="d-flex justify-between py-1 text-info"><span>• Par ans :</span>
                                    <strong>{{ number_format($ventesAn ?? 0, 0, ',', ' ') }} FCFA</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <strong class="text-dark font-weight-bold">Opérateurs</strong>
                            <hr class="my-2">
                            <ul class="list-unstyled mb-0" style="font-size: 14px;">
                                <li class="d-flex justify-between py-1 text-secondary"><span><i
                                            class="fa fa-user text-primary mr-2"></i> Clients :</span>
                                    <strong>{{ $nbClients ?? 0 }} Clients</strong></li>
                                <li class="d-flex justify-between py-1 text-secondary"><span><i
                                            class="fa fa-truck text-danger mr-2"></i> Fournisseurs :</span>
                                    <strong>{{ $nbFournisseurs ?? 0 }} Fournisseurs</strong></li>
                                <li class="d-flex justify-between py-1 text-secondary"><span><i
                                            class="fa fa-stethoscope text-dark mr-2"></i> Vétérinaires :</span>
                                    <strong>{{ $nbVeterinaires ?? 0 }} Vétérinaires</strong></li>
                                <li class="d-flex justify-between py-1 text-secondary"><span><i
                                            class="fa fa-home text-info mr-2"></i> Poulaillers :</span>
                                    <strong>{{ $nbPoulaillers ?? 0 }} Poulailler</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-12">
                    <div class="text-white font-weight-bold text-center py-2 rounded shadow-sm"
                        style="background-color: #5c6bc0; font-size: 18px; letter-spacing: 1px;">
                        Pertes
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                {{-- 1. Perte par An --}}
                <div class="col-md-3 mb-2">
                    <div class="card card-body border-0 shadow-sm h-100 text-center p-3">
                        <h6 class="text-muted text-uppercase font-xs font-weight-bold">Pertes par An</h6>
                        <hr class="my-2">
                        @forelse($perteAn as $pa)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-left font-sm"><strong>{{ $pa->pro_nom }}</strong> <small
                                        class="text-muted">({{ $pa->pro_type }})</small></span>
                                <span class="badge badge-danger p-2">{{ (int) $pa->total_qte }} Tête(s)/KG/Flacon(s)</span>
                            </div>
                        @empty
                            <strong class="text-success py-2">0 Tête</strong>
                        @endforelse
                    </div>
                </div>

                {{-- 2. Perte par Mois --}}
                <div class="col-md-3 mb-2">
                    <div class="card card-body border-0 shadow-sm h-100 text-center p-3">
                        <h6 class="text-muted text-uppercase font-xs font-weight-bold">Pertes par Mois</h6>
                        <hr class="my-2">
                        @forelse($perteMois as $pm)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-left font-sm"><strong>{{ $pm->pro_nom }}</strong> <small
                                        class="text-muted">({{ $pm->pro_type }})</small></span>
                                <span class="badge badge-danger p-2">{{ (int) $pm->total_qte }} Tête(s)/KG/Flacon(s)</span>
                            </div>
                        @empty
                            <strong class="text-success py-2">0 Tête</strong>
                        @endforelse
                    </div>
                </div>

                {{-- 3. Perte par Semaine --}}
                <div class="col-md-3 mb-2">
                    <div class="card card-body border-0 shadow-sm h-100 text-center p-3">
                        <h6 class="text-muted text-uppercase font-xs font-weight-bold">Pertes par Semaine</h6>
                        <hr class="my-2">
                        @forelse($perteSemaine as $ps)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-left font-sm"><strong>{{ $ps->pro_nom }}</strong> <small
                                        class="text-muted">({{ $ps->pro_type }})</small></span>
                                <span class="badge badge-danger p-2">{{ (int) $ps->total_qte }} Tête(s)/KG/Flacon(s)</span>
                            </div>
                        @empty
                            <strong class="text-success py-2">0 Tête</strong>
                        @endforelse
                    </div>
                </div>

                {{-- 4. Perte par Jour --}}
                <div class="col-md-3 mb-2">
                    <div class="card card-body border-0 shadow-sm h-100 text-center p-3">
                        <h6 class="text-muted text-uppercase font-xs font-weight-bold">Pertes par Jour</h6>
                        <hr class="my-2">
                        @forelse($perteJour as $pj)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-left font-sm"><strong>{{ $pj->pro_nom }}</strong> <small
                                        class="text-muted">({{ $pj->pro_type }})</small></span>
                                <span class="badge badge-danger p-2">{{ (int) $pj->total_qte }} Tête(s)/KG/Flacon(s)</span>
                            </div>
                        @empty
                            <strong class="text-success py-2">0 Tête(s)/KG/Flacon(s)</strong>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-12">
                    <div class="text-white font-weight-bold text-center py-2 rounded shadow-sm"
                        style="background-color: #5c6bc0; font-size: 18px; letter-spacing: 1px;">
                        Vaccinations
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-dark mb-2">Vaccinations par ans</h6>
                            <table class="table table-sm table-striped border mb-0" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date vaccination</th>
                                        <th>Vaccin</th>
                                        <th>Qte Vaccin</th>
                                        <th>Poulailler</th>
                                        <th>Vétérinaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vaccinationsAn ?? [] as $vac)
                                        <tr>
                                            <td class="text-primary"><span style="color: #5c6bc0;">●</span>
                                                {{ Carbon\Carbon::parse($vac->created_at)->format('Y-m-d') }}</td>
                                            <td>{{ $vac->produit->pro_nom }}</td>
                                            <td>{{ $vac->vac_qte }} Flacon</td>
                                            <td>{{ $vac->poulailler->poul_nom ?? 'N/A' }}</td>
                                            <td>{{ $vac->veterinaire->vtr_nom ?? 'N/A' }} {{ $vac->veterinaire->vtr_prenom ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Aucun historique annuel</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-dark mb-2">Vaccinations par mois</h6>
                            <table class="table table-sm table-striped border mb-0" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date vaccination</th>
                                        <th>Vaccin</th>
                                        <th>Qte Vaccin</th>
                                        <th>Poulailler</th>
                                        <th>Vétérinaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vaccinationsMois ?? [] as $vac)
                                        <tr>
                                            <td class="text-primary"><span style="color: #5c6bc0;">●</span>
                                                {{ Carbon\Carbon::parse($vac->created_at)->format('Y-m-d') }}</td>
                                            <td>{{ $vac->produit->pro_nom }}</td>
                                            <td>{{ $vac->vac_qte }} Flacon</td>
                                            <td>{{ $vac->poulailler->poul_nom ?? 'N/A' }}</td>
                                            <td>{{ $vac->veterinaire->vtr_nom ?? 'N/A' }} {{ $vac->veterinaire->vtr_prenom ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Aucune vaccination ce
                                                mois-ci</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-dark mb-2">Vaccinations par semaine</h6>
                            <table class="table table-sm table-striped border mb-0" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date vaccination</th>
                                        <th>Vaccin</th>
                                        <th>Qte Vaccin</th>
                                        <th>Poulailler</th>
                                        <th>Vétérinaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vaccinationsSemaine ?? [] as $vac)
                                        <tr>
                                            <td class="text-primary"><span style="color: #5c6bc0;">●</span>
                                                {{ Carbon\Carbon::parse($vac->created_at)->format('Y-m-d') }}</td>
                                            <td>{{ $vac->produit->pro_nom }}</td>
                                            <td>{{ $vac->vac_qte }} Flacon</td>
                                            <td>{{ $vac->poulailler->poul_nom ?? 'N/A' }}</td>
                                            <td>{{ $vac->veterinaire->vtr_nom ?? 'N/A' }} {{ $vac->veterinaire->vtr_prenom ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Aucune vaccination cette
                                                semaine</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-dark mb-2">Vaccinations par jour</h6>
                            <table class="table table-sm table-striped border mb-0" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date vaccination</th>
                                        <th>Vaccin</th>
                                        <th>Qte Vaccin</th>
                                        <th>Poulailler</th>
                                        <th>Vétérinaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vaccinationsJour ?? [] as $vac)
                                        <tr>
                                            <td class="text-primary"><span style="color: #5c6bc0;">●</span>
                                                {{ Carbon\Carbon::parse($vac->created_at)->format('Y-m-d') }}</td>
                                            <td>{{ $vac->produit->pro_nom }}</td>
                                            <td>{{ $vac->vac_qte }} Flacon</td>
                                            <td>{{ $vac->poulailler->poul_nom ?? 'N/A' }}</td>
                                            <td>{{ $vac->veterinaire->vtr_nom ?? 'N/A' }} {{ $vac->veterinaire->vtr_prenom ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Aucune vaccination
                                                aujourd'hui</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-12">
                    <div class="text-white font-weight-bold text-center py-2 rounded shadow-sm"
                        style="background-color: #3b50df; font-size: 16px;">
                        Stock actuelle des produits
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($stocksProduits ?? [] as $prod)
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3"
                                        style="font-size: 14px;">
                                        <span class="text-muted"><span class="text-primary mr-2">●</span>
                                            {{ $prod->pro_nom }} {{ $prod->pro_type }}:</span>
                                        <strong class="text-dark">{{ $prod->pro_stock }} (Unités = KG / Tête(s)/Flacon(s))</strong>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted py-3">Aucun produit en stock
                                        actuellement.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Classes utilitaires d'alignement --}}
    <style>
        .justify-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
@endsection
