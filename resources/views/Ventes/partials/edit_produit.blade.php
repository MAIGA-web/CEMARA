@extends('layout.header')

@section('contenu')
<div class="content text-center">
    <div class="card col-md-6 offset-md-3">
        <div class="card-header">
            <strong>Modifier le produit dans la vente #{{ $venteSelectionnee->id }}</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('vendre.update', $vendre->id) }}" method="POST">
                @csrf
                <div class="form-group text-left">
                    <label>Produit</label>
                    <select name="pro_id" class="form-control">
                        @foreach($produits as $p)
                            <option value="{{ $p->id }}" {{ $vendre->pro_id == $p->id ? 'selected' : '' }}>
                                {{ $p->pro_nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group text-left">
                    <label>Prix Unitaire</label>
                    <input type="number" step="0.01" name="vdr_pu" class="form-control" value="{{ $vendre->vdr_pu }}">
                </div>
                <div class="form-group text-left">
                    <label>Quantité</label>
                    <input type="number" name="vdr_qte" class="form-control" value="{{ $vendre->vdr_qte }}">
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    <a href="{{ route('ventes.index', ['details' => $vendre->vte_id]) }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection