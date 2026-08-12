@extends('layout.header')

@section('contenu')
    <div class="content">
        <div class="card col-md-6 offset-md-3">
            <div class="card-header"><strong>Modifier le Reglement</strong></div>
            <div class="card-body">
                @if (session('error_messages'))
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i> {{ session('error_messages') }}
                    </div>
                @endif
                <form action="{{ route('reglement.update', $reglement->id) }}" method="POST">

                    @csrf
                    <div class="form-group">
                        <label>Montant Versé</label>
                        <input type="number" name="re_mnt" class="form-control" 
                                value="{{old('re_mnt',$reglement->re_mnt) }}">
                    </div>
                    <div class="form-group">
                        <label>Mode de paiement</label>
                        <select name="mod_id" class="form-control">
                            @foreach ($modes as $m)
                                <option value="{{ $m->id }}" {{ $reglement->mod_id == $m->id ? 'selected' : '' }}>
                                    {{ $m->mod_nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                   <a href="{{ route('achat.index', ['details' => $reglement->ac_id, 'tab' => 'pills-paie']) }}" class="btn btn-secondary">Retour</a>
                </form>
            </div>
        </div>
    </div>
@endsection
