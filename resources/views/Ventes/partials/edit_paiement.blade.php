@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="card col-md-6 offset-md-3">
            @if (session('error_message'))
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> {{ session('error_message') }}
                </div>
            @endif
            <div class="card-header"><strong>Modifier le paiement</strong></div>
            <div class="card-body">
                <form action="{{ route('paiement.update', $paiement->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Montant Versé</label>
                        <input type="number" name="pa_payer" class="form-control"
                            value="{{ old('pa_payer', $paiement->pa_payer) }}">
                    </div>
                    <div class="form-group">
                        <label>Mode de paiement</label>
                        <select name="mod_id" class="form-control">
                            @foreach ($modes as $m)
                                <option value="{{ $m->id }}" {{ $paiement->mod_id == $m->id ? 'selected' : '' }}>
                                    {{ $m->mod_nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('ventes.index', ['details' => $vente->id]) }}" class="btn btn-secondary">Retour</a>
                </form>
            </div>
        </div>
    </div>
@endsection
