<div class="col-xl-4 col-lg-5">
    @if (session('success_message') || $errors->any())
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            {{ session('success_message') ?? 'Veuillez corriger les erreurs du formulaire.' }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between py-3">
            <strong class="text-white mb-0"><i class="fa fa-cubes mr-2"></i> Lots de Poussins</strong>
            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalNouveauLot">
                <i class="fa fa-plus"></i> Nouveau Lot
            </button>
        </div>
        <div class="card-body p-0" style="max-height: 70vh; overflow-y: auto;">
            <div class="list-group list-group-flush">
                @forelse($lots as $l)
                    <div
                        class="list-group-item list-group-item-action p-3 position-relative {{ isset($lotSelectionne) && $lotSelectionne->id == $l->id ? 'bg-light border-left-primary font-weight-bold' : '' }}">
                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                            {{-- Lien cliquable sur le code du lot --}}
                            <a href="{{ route('lots.index', ['lot_id' => $l->id]) }}"
                                class="text-dark h6 mb-0 font-weight-bold d-block flex-grow-1">
                                {{ $l->lot_code }}
                            </a>
                            <div class="d-flex align-items-center">
                                <span
                                    class="badge {{ $l->lot_actif ? 'badge-success' : 'badge-secondary' }} px-2 py-1 mr-2">
                                    {{ $l->lot_actif ? 'En cours' : 'Clôturé' }}
                                </span>
                                {{-- Bouton Édition du Lot --}}
                                @if ($l->lot_actif == true)
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-1" data-toggle="modal"
                                        data-target="#modalEditLot{{ $l->id }}">
                                        <i class="fa fa-cog"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('lots.index', ['lot_id' => $l->id]) }}"
                            class="text-muted text-decoration-none d-block">
                            <p class="mb-1 text-muted font-sm">
                                <i class="fa fa-tag text-primary"></i>
                                <strong>{{ $l->produit->pro_nom }} {{ $l->produit->pro_type }}</strong> <br>
                                <i class="fa fa-home text-secondary"></i>
                                {{ $l->poulailler->poul_nom ?? 'Sans Poulailler' }} <br>
                                <i class="fa fa-calendar-check-o"></i> Arrivée :
                                {{ $l->lot_date_arrivee->format('d/m/Y') }}
                            </p>
                            <div
                                class="d-flex w-100 justify-content-between align-items-center font-xs text-secondary mt-2">
                                <span>Initial : <strong>{{ number_format($l->lot_qte_initiale, 0, ',', ' ') }}</strong>
                                    têtes</span>
                                <span class="text-primary">Âge : {{ $l->age_poussins }} Jours</span>
                            </div>
                        </a>
                    </div>

                    {{-- MODAL D'ÉDITION / SUPPRESSION DU LOT SPÉCIFIQUE --}}
                    <div class="modal fade" id="modalEditLot{{ $l->id }}" tabindex="-1" role="dialog"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-secondary text-white p-3">
                                    <h5 class="modal-title text-white font-weight-bold"><i class="fa fa-edit mr-2"></i>
                                        Modifier le Lot {{ $l->lot_code }}</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('lots.updateLot', $l->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4 text-left">
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold text-secondary font-xs text-uppercase">Code
                                                Unique</label>
                                            <input type="text" name="lot_code" class="form-control"
                                                value="{{ $l->lot_code }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label
                                                class="font-weight-bold text-secondary font-xs text-uppercase">Origine</label>
                                            <input type="text" name="origine" class="form-control"
                                                value="{{ $l->origine }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label
                                                class="font-weight-bold text-secondary font-xs text-uppercase">Poulailler</label>
                                            <select name="poul_id" class="form-control custom-select" required>
                                                @foreach ($poulaillers as $p)
                                                    <option value="{{ $p->id }}"
                                                        {{ $l->poul_id == $p->id ? 'selected' : '' }}>
                                                        {{ $p->poul_nom }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label
                                                class="font-weight-bold text-secondary font-xs text-uppercase">Produit</label>
                                            <select name="pro_id" class="form-control custom-select" required>
                                                @foreach ($produits as $pr)
                                                    <option value="{{ $pr->id }}"
                                                        {{ $l->pro_id == $pr->id ? 'selected' : '' }}>
                                                        {{ $pr->pro_nom }} {{ $pr->pro_type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-6 mb-3">
                                                <label
                                                    class="font-weight-bold text-secondary font-xs text-uppercase">Quantité
                                                    Initiale</label>
                                                <input type="number" name="lot_qte_initiale" class="form-control"
                                                    value="{{ $l->lot_qte_initiale }}" required>
                                            </div>
                                            <div class="form-group col-6 mb-3">
                                                <label
                                                    class="font-weight-bold text-secondary font-xs text-uppercase">Date
                                                    d'arrivée</label>
                                                <input type="date" name="lot_date_arrivee" class="form-control"
                                                    value="{{ $l->lot_date_arrivee->format('Y-m-d') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                                        <button type="button" class="btn btn-danger font-weight-bold"
                                            onclick="if(confirm('Supprimer définitivement ce lot et TOUT son historique ?')) { document.getElementById('delete-lot-form-{{ $l->id }}').submit(); }">
                                            <i class="fa fa-trash"></i> Supprimer
                                        </button>
                                        <div>
                                            <button type="button" class="btn btn-secondary font-weight-bold"
                                                data-dismiss="modal">Annuler</button>
                                            <button type="submit"
                                                class="btn btn-primary font-weight-bold shadow-sm">Sauvegarder</button>
                                        </div>
                                    </div>
                                </form>
                                {{-- Formulaire invisible pour la suppression physique --}}
                                <form id="delete-lot-form-{{ $l->id }}"
                                    action="{{ route('lots.deleteLot', $l->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-folder-open-o fa-2x mb-2 d-block"></i> Aucun lot enregistré.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
