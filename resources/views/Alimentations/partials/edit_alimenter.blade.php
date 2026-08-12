
    <div class="card border-primary">
        <div class="card-header bg-dark text-white">
            <strong>Modifier la quantité distribuée pour : {{ $alimenterEnEdition->produit->pro_nom }}</strong>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ url('/Alimentations') }}">
                @csrf
                <input type="hidden" name="emp" value="AU">
                <input type="hidden" name="almt_id" value="{{ $alimenterEnEdition->id }}">
                <input type="hidden" name="alm_id" value="{{ $alimentationSelectionnee->id }}">

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-control-label">Nouvelle Quantité</label>
                        <input type="number" name="almt_qte" value="{{ $alimenterEnEdition->almt_qte }}"
                            class="form-control" min="1" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-control-label">Aliment (Lecture Seule)</label>
                        <select name="pro_id" class="form-control" readonly>
                            <option value="{{ $alimenterEnEdition->pro_id }}">
                                {{ $alimenterEnEdition->produit->pro_nom }}</option>
                        </select>
                    </div>
                </div>
                <div class="text-right">
                    <a href="{{ url('/Alimentations?alm_id=' . $alimentationSelectionnee->id) }}"
                        class="btn btn-secondary btn-sm">Annuler</a>
                    <button type="submit" name="valider" value="Valider" class="btn btn-primary btn-sm">Enregistrer les
                        modifications</button>
                </div>
            </form>
        </div>
    </div>
