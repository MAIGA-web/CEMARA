<div class="modal fade" id="modalNouveauLot" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white p-3">
                <h5 class="modal-title text-white font-weight-bold"><i class="fa fa-plus-circle mr-2"></i> Lancer une
                    nouvelle bande</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('lots.storeLot') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-secondary font-xs text-uppercase">Code du Lot Unique</label>
                        <input type="text" name="lot_code" class="form-control" placeholder="Ex: LOT-CHAIR-2026-A"
                            required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-secondary font-xs text-uppercase">Origine</label>
                        <input type="text" name="origine" class="form-control"
                            placeholder="Ex: Éclosion interne ou externe" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-secondary font-xs text-uppercase">Poulailler de
                            destination</label>
                        <select name="poul_id" class="form-control custom-select" required>
                            <option value="">-- Sélectionner un bâtiment --</option>
                            @foreach ($poulaillers as $p)
                                <option value="{{ $p->id }}">{{ $p->poul_nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-secondary font-xs text-uppercase">Type de Produit
                            (Arrivage)</label>
                        <select name="pro_id" class="form-control custom-select" required>
                            <option value="">-- Sélectionner le produit reçu --</option>
                            @foreach ($produits as $p)
                                <option value="{{ $p->id }}">{{ $p->pro_nom}} {{ $p->pro_type}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="form-group col-6 mb-3">
                            <label class="font-weight-bold text-secondary font-xs text-uppercase">Quantité reçue</label>
                            <input type="number" name="lot_qte_initiale" min="1" class="form-control"
                                placeholder="Ex: 1000" required>
                        </div>
                        <div class="form-group col-6 mb-3">
                            <label class="font-weight-bold text-secondary font-xs text-uppercase">Durée d'élevage
                                prévue</label>
                            <div class="input-group">
                                <input type="number" name="duree_elevage" min="1" class="form-control"
                                    value="45" required>
                                <div class="input-group-append"><span
                                        class="input-group-text bg-light font-xs">Jours</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-secondary font-xs text-uppercase">Date d'arrivée des
                            poussins</label>
                        <input type="date" name="lot_date_arrivee" class="form-control"
                            value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary font-weight-bold"
                        data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary font-weight-bold shadow-sm">Créer le Lot</button>
                </div>
            </form>
        </div>
    </div>
</div>
