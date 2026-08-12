@extends('layout.header')

@section('contenu')
<div class="content">
    <div class="card col-md-6 offset-md-3">
        <div class="card-header">
            <strong>{{ $vente->id ? 'Modifier la vente n°' . $vente->id : 'Créer une nouvelle vente' }}</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('ventes.create', $vente->id) }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label>Client</label>
                    <select name="cl_id" class="form-control">
                        @foreach($clients as $client)
                        @if ($client->cl_etat==true)
                            
                        <option value="{{ $client->id }}" {{ $vente->cl_id == $client->id ? 'selected' : '' }}>
                            {{ $client->cl_nom }} {{ $client->cl_prenom }}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
                @if ($vente->id)
                <div class="form-group">
                    <label>Date vente</label>
                    <div class="form-check">
                        <input type="text" name="created_at" class="form-control" value="{{ $vente->created_at->format('d/m/Y')}}">
                        {{-- <label class="form-check-label">Vente clôturée / payée</label> --}}
                    </div>
                </div>
                    
                @endif

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ $vente->id ? 'Mettre à jour' : 'Créer la vente' }}
                    </button>
                    <a href="{{ route('ventes.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection