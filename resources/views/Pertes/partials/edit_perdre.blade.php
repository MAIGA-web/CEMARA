<div class="card border-danger">
    <div class="card-header bg-dark text-white">
        <strong>Modifier la perte de : {{ $perdreEnEdition->produit->pro_nom }}</strong>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('/Pertes') }}">
            @csrf
            <input type="hidden" name="emp" value="PU">
            <input type="hidden" name="perd_id" value="{{ $perdreEnEdition->id }}">
            <input type="hidden" name="per_id" value="{{ $perteSelectionnee->id }}">
            <input type="hidden" name="pro_id" value="{{ $perdreEnEdition->pro_id }}">
            
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-control-label">Nouvelle Quantité</label>
                    <input type="number" name="perd_qte" value="{{ $perdreEnEdition->perd_qte }}" class="form-control" min="1" required>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-control-label">Motif</label>
                    <input type="text" name="motif" value="{{ $perdreEnEdition->motif }}" class="form-control">
                </div>
            </div>
            <div class="text-right">
                <a href="{{ url('/Pertes?per_id='.$perteSelectionnee->id) }}" class="btn btn-secondary btn-sm">Annuler</a>
                <button type="submit" name="valider" value="Valider" class="btn btn-danger btn-sm">Enregistrer</button>
            </div>
        </form>
    </div>
</div>