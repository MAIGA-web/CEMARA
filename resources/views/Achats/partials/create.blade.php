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
							@if ($acheter->id)
							Modification contenu vente
							@else Nouvelle contenu vente
							@endif
						</div>
						<div class="card-body card-block">
							<form method="POST" enctype="multipart/form-data"
      action="{{ $acheter->id ? url('/Achats/Vendre/vente/add-edit/'.$acheter->id) : url('/Ventes/Vendre/vente/add-edit/'.$achat->id) }}">
    @csrf
    
    {{-- On garde l'ID de la vente dans un champ caché --}}
    <input type="hidden" name="ac_id" value="{{ $achat->id }}">
								<input type="hidden" name="ac_id" value="{{ $acheter->ac_id ?? request()->query('ac_id') }}">
								@csrf
								<div class="form-group ">
									<label class=" form-control-label">Produit</label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-user"></i>
										</div>
										<select id="selectLg" name="pro_id" class="form-control-lg form-control">
											<option selected>Selectionner</option>
											@foreach($produits as $value_produit)
												<option value="{{ $value_produit->id }}"
													{{ (isset($acheter) && $acheter->pro_id == $value_produit->id) ? 'selected' : '' }}>
													{{ $value_produit->pro_nom }}
												</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group ">
									<label class=" form-control-label">Prix unitaire</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-user"></i></div>
										<input type="number" class="form-control" name="act_pu" placeholder="Prix unitaire"
											@if ($acheter['act_pu']) value="{{ $acheter['act_pu'] }}"
											@else value="{{ old('act_pu') }}"
											@endif>
									</div>
								</div>
								<div class="form-group ">
									<label class=" form-control-label">Quantité</label>
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-user"></i></div>
										<input type="text" class="form-control" name="act_qte" placeholder="Quantité"
											@if ($acheter['act_qte']) value="{{ $acheter['act_qte'] }}"
											@else value="{{ old('act_qte') }}"
											@endif>
									</div>
								</div>
						</div>
						<div class="modal-footer">
							<a href="{{ url('/Achats') }}" class="btn btn-danger mb-1">
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