<div class="card" style="margin-top: -8em">
    <div class="card-header bg-dark text-white">
        <strong>Modification</strong>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('/Collections') }}">
            @csrf
            <input type="hidden" name="emp" value="CU">
            <input type="hidden" name="coll_id" value="{{ $collecterEnEdition->id }}">
            <input type="hidden" name="col_id" value="{{ $collectionSelectionnee->id }}">

            {{-- <div class="row"> --}}
                <div class="form-group col-md-12">
                    <label class="form-control-label">Total Ramassés </label>
                    <input type="number" name="qte_ramasse" class="form-control" min="0" placeholder="Ex: 500"
                        value="{{ $collecterEnEdition->qte_ramasse }}" required>
                </div>
                <div class="form-group col-md-12">
                    <label class="form-control-label">Dont Œufs Cassés / Fêlés</label>
                    <input type="number" name="qte_casse" class="form-control" min="0"
                        value="{{ $collecterEnEdition->qte_casse }}" required>
                </div>

                <div class="form-group col-md-12">
                    <label class="form-control-label">Dont Œufs Consommés (Manger)</label>
                    <input type="number" name="qte_consomme" class="form-control" min="0"
                        value="{{ $collecterEnEdition->qte_consomme }}" required>
                </div>
            {{-- </div> --}}
            <div class="text-right">
                <a href="{{ url('/Collections?col_id=' . $collectionSelectionnee->id) }}"
                    class="btn btn-secondary btn-sm">Annuler</a>
                <button type="submit" name="valider" value="Valider" class="btn btn-success btn-sm">Enregistrer la
                    modification</button>
            </div>
        </form>
    </div>
</div>
