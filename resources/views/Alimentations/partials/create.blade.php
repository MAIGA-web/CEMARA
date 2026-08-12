
                            <form method="POST" action="{{ url('/Alimentations') }}">
                                @csrf
                                <input type="hidden" name="emp" value="AC">
                                <input type="hidden" name="alm_id" value="{{ $alimentationSelectionnee->id }}">
                                <input type="hidden" name="fer_id" value="{{ session('fer_id') }}">

                                <div class="form-group">
                                    <label class="form-control-label">Sélectionner l'Aliment</label>
                                    <select name="pro_id" class="form-control" required>
                                        <option value="">-- Choisir un aliment --</option>
                                        @foreach ($produits as $prod)
                                            <option value="{{ $prod->id }}">{{ $prod->pro_nom }} (Stock:
                                                {{ $prod->pro_stock }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Quantité à distribuer</label>
                                    <input type="number" name="almt_qte" class="form-control" min="1"
                                        placeholder="Ex: 15" required>
                                </div>

                                <button type="submit" name="valider" value="Valider"
                                    class="btn btn-success btn-sm btn-block">
                                    <i class="fa fa-save"></i> Valider l'ajout
                                </button>
                            </form>
                      