<form method="POST" action="{{ url('/Collections') }}">
    @csrf
    <input type="hidden" name="emp" value="CC">
    <input type="hidden" name="col_id" value="{{ $collectionSelectionnee->id }}">

    <div class="form-group">
        <label class="form-control-label">Total Ramassé </label>
        <input type="number" name="qte_ramasse" class="form-control" min="0" placeholder="Ex: 500" required>
    </div>

    <div class="form-group">
        <label class="form-control-label">Dont Œufs Cassés / Fêlés</label>
        <input type="number" name="qte_casse" class="form-control" min="0" placeholder="Ex: 15" value="0" required>
    </div>

    <div class="form-group">
        <label class="form-control-label">Dont Œufs Consommés (Manger)</label>
        <input type="number" name="qte_consomme" class="form-control" min="0" placeholder="Ex: 5" value="0" required>
    </div>

    <button type="submit" name="valider" value="Valider" class="btn btn-success btn-sm btn-block">
        <i class="fa fa-save"></i> Enregistrer les chiffres
    </button>
</form>