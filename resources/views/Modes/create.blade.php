@extends('layout.header')

<div id="right-panel" class="right-panel">
	<div class="content">
		<div class="animated fadeIn">
			<div class="row">
				<div class="col-4"></div>
				<div class="col-8">
					<div class="card col-6">
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
							@if ($mode['id'])
							Modification Mode
							@else Nouveau Mode
							@endif
						</div>
						<div class="card-body card-block">
							<form method="POST" enctype="multipart/form-data"
								{{-- action="{{ (isset($modes) && $mode->exists) ? url('/Fournisseurs/add-edit/'.$modes->id) : url('/Fournisseurs/add-edit') }}"> --}}
								action="{{ $mode->id ? url('/Modes/add-edit/'.$mode->id) : url('/Modes/add-edit') }}">
								@csrf
								<div class="form-group ">
									<label class=" form-control-label">Mode nom</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-user"></i></div>
										<input type="text" class="form-control" name="mod_nom" placeholder=" Mode nom"
											@if ($mode['mod_nom']) value="{{ $mode['mod_nom'] }}"
											@else value="{{ old('mod_nom') }}"
											@endif>
									</div>
								</div>
						</div>
						<div class="modal-footer">
							<a href="{{ url('/Modes') }}" class="btn btn-danger mb-1">
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