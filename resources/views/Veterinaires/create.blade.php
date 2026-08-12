@extends('layout.header')
@section('contenu')
<div id="right-panel" class="right-panel">
	<div class="content">
		<div class="animated fadeIn">
			<div class="row">

				{{-- <div class="col-4"></div> --}}
				<div class="col-12">
					<div class="card">
						@if ($errors->any())
							<div class="alert alert-danger">
								<ul>
									@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif

						<div class="card-header"> 
                            @if ($veterinaire['id'])
                                
                            Modification veterinaire 
                            @else Nouveau veterinaire
                            @endif
                             

                        </div>
						<div class="card-body card-block">
							<form method="POST" enctype="multipart/form-data"
								{{-- action="{{ (isset($veterinaires) && $veterinaire->exists) ? url('/veterinaire/add-edit/'.$veterinaires->id) : url('/veterinaire/add-edit') }}"> --}}
                                action="{{ $veterinaire->id ? url('/Veterinaires/add-edit/'.$veterinaire->id) : url('/Veterinaires/add-edit') }}">
								@csrf
								<div class="form-group ">
									<label class=" form-control-label">Nom</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-user"></i></div>
										<input type="text" class="form-control" name="vtr_nom" placeholder="Nom"
											@if ($veterinaire['vtr_nom']) value="{{ $veterinaire['vtr_nom'] }}"
											@else value="{{ old('vtr_nom') }}"
											@endif>
									</div>
								</div>
								<div class="form-group ">
									<label class=" form-control-label">Prénom</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-user"></i></div>
										<input type="text" class="form-control" name="vtr_prenom" placeholder="Prénom"
											@if ($veterinaire['vtr_prenom']) value="{{ $veterinaire['vtr_prenom'] }}"
											@else value="{{ old('vtr_prenom') }}"
											@endif>
									</div>
								</div>

								<div class="form-group ">
									<label class=" form-control-label">Adresse</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-home"></i></div>
										<input type="text" class="form-control" name="vtr_adresse" placeholder="Adresse"
											@if ($veterinaire['vtr_adresse']) value="{{ $veterinaire['vtr_adresse'] }}"
											@else value="{{ old('vtr_adresse') }}"
											@endif>
									</div>
								</div>

                                	<div class="form-group ">
									<label class=" form-control-label">Tel</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-phone"></i></div>
										<input type="text" class="form-control" name="vtr_tel" placeholder="Tel "
											@if ($veterinaire['vtr_tel']) value="{{ $veterinaire['vtr_tel'] }}"
											@else value="{{ old('vtr_tel') }}"
											@endif maxlength="8">
									</div>
								</div>

                            @if($veterinaire->id)
                            <div class="form-group">
                                <label class="form-control-label">État du compte</label>
                                <div class="input-group">
                                    <div class="radio-inline">
                                        <input type="radio" name="vtr_etat" value="1" {{ $veterinaire->vtr_etat ? 'checked' : '' }}>
                                        <label class="ml-1 mr-3">Actif</label>
                                        
                                        <input type="radio" name="vtr_etat" value="0" {{ !$veterinaire->vtr_etat ? 'checked' : '' }}>
                                        <label class="ml-1">Inactif</label>
                                    </div>
                                </div>
                            </div>
                            @endif

								<div class="form-group ">
                                    {{-- <div class="input-group-addon"><i class="fa fa-user"></i></div> --}}
									<label class=" form-control-label">Sexe</label>
									<div class="checkbox">
										
										<input type="radio" name="vtr_sexe" value="M"
											{{ $veterinaire->vtr_sexe == 'M' ? 'checked' : '' }}>
										<label class=" form-control-label">Masculin</label>
										<div class="checkbox">
											{{-- <div class="input-group-addon"><i class="fa fa-user"></i></div> --}}
											<input type="radio" name="vtr_sexe" value="F"
											{{ $veterinaire->vtr_sexe == 'F' ? 'checked' : '' }}>
											<label>
												Féminin
											</label>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<a href="{{ url('/Veterinaires') }}" class="btn btn-danger mb-1">
										Annuler
									</a>
									<button type="submit" class="btn btn-primary">Valider</button>
								</div>
							</form>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection;