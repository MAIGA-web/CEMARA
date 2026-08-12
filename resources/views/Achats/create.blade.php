@extends('layout.header')

@section('contenu')
<div class="content">
    <div class="card col-md-6 offset-md-3">
        <div class="card-header">
            <strong>{{ $achat->id ? 'Modifier l\'achat n°' . $achat->id : 'Créer un nouveau achat' }}</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('achat.create', $achat->id) }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label>fournisseur</label>
                    <select name="four_id" class="form-control">
                        @foreach($fournisseur as $four)
                        @if ($four->four_etat==true)
                        <option value="{{ $four->id }}" {{ $achat->four_id == $four->id ? 'selected' : '' }}>
                            {{ $four->four_nom }} {{ $four->four_prenom }}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
                @if ($achat->id)
                <div class="form-group">
                    <label>Date achat</label>
                    <div class="form-check">
                        <input type="text" name="created_at" class="form-control" value="{{ $achat->created_at->format('d/m/Y')}}">
                        {{-- <label class="form-check-label">achat clôturée / payée</label> --}}
                    </div>
                </div>
                    
                @endif

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ $achat->id ? 'Mettre à jour' : 'Créer la achat' }}
                    </button>
                    <a href="{{ route('achat.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection