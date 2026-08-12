<div id="right-panel" class="right-panel">
    <div class="content">
        <div class="animated fadeIn">
            <div class="row">

                <div class="col-4"></div>
                <div class="col-8">
                    <div class="card col-6">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card-header">
                            {{-- @if ($client['id'])
                                Modification client
                            @else
                                Nouveau client
                            @endif --}}


                        </div>
                        <div class="card-body card-block">
                            <form action="{{ route('lots.storeLot') }}" method="POST">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-secondary font-xs text-uppercase">Code du
                                            Lot
                                            Unique</label>
                                        <input type="text" name="lot_code" class="form-control"
                                            placeholder="Ex: LOT-CHAIR-2026-A" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-secondary font-xs text-uppercase">Origine /
                                            Fournisseur des
                                            poussins</label>
                                        <input type="text" name="origine" class="form-control"
                                            placeholder="Ex: Couvoir Moderne du Mali" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-secondary font-xs text-uppercase">Poulailler
                                            de
                                            destination</label>
                                        <select name="poul_id" class="form-control custom-select" required>
                                            <option value="">-- Sélectionner un bâtiment --</option>
                                            @foreach ($poulaillers as $p)
                                                <option value="{{ $p->id }}">{{ $p->poul_nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-6 mb-3">
                                            <label
                                                class="font-weight-bold text-secondary font-xs text-uppercase">Quantité
                                                reçue</label>
                                            <input type="number" name="lot_qte_initiale" min="1"
                                                class="form-control" placeholder="Ex: 1000" required>
                                        </div>
                                        <div class="form-group col-6 mb-3">
                                            <label class="font-weight-bold text-secondary font-xs text-uppercase">Durée
                                                d'élevage
                                                prévue</label>
                                            <div class="input-group">
                                                <input type="number" name="duree_elevage" min="1"
                                                    class="form-control" value="45" required>
                                                <div class="input-group-append"><span
                                                        class="input-group-text bg-light font-xs">Jours</span></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold text-secondary font-xs text-uppercase">Date
                                            d'arrivée des
                                            poussins</label>
                                        <input type="date" name="lot_date_arrivee" class="form-control"
                                            value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light p-3">
                                    <button type="button" class="btn btn-secondary font-weight-bold"
                                        data-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary font-weight-bold shadow-sm">Créer le
                                        Lot</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
