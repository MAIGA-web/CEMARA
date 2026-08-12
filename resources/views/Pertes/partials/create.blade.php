<form method="POST" action="{{ url('/Pertes') }}">
    @csrf
    <input type="hidden" name="emp" value="PC">
    <input type="hidden" name="per_id" value="{{ $perteSelectionnee->id }}">

    <div class="form-group">
        <label class="form-control-label">Produit Perdu / Cassé</label>
        <select name="pro_id" class="form-control" required>
            <option value="">-- Choisir le produit --</option>
            @foreach($produits as $prod)
                <option value="{{ $prod->id }}">{{ $prod->pro_nom }} (Stock actuel: {{ $prod->pro_stock }})</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-control-label">Quantité perdue</label>
        <input type="number" name="perd_qte" class="form-control" min="1" required>
    </div>

    <div class="form-group">
        <label class="form-control-label">Motif / Raison</label>
        <input type="text" name="motif" class="form-control" placeholder="Ex: Œufs cassés, Sac humide...">
    </div>

    <button type="submit" name="valider" value="Valider" class="btn btn-danger btn-sm btn-block">
        <i class="fa fa-save"></i> Enregistrer la perte
    </button>
</form>