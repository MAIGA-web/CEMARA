@extends('layout.header')

@section('contenu')
    <div class="content mt-3">
        <div class="animated fadeIn">

            {{-- Notifications de succès ou d'erreur --}}
            {{-- @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="fa fa-exclamation-triangle mr-2"></i>
                    <strong>Erreurs de validation :</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif --}}

            @if (session('error_message') || $errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="fa fa-exclamation-triangle mr-2"></i>
                    {{ session('error_message') ?? 'Veuillez corriger les erreurs du formulaire.' }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                {{-- PANNEAU GAUCHE : LISTE DES LOTS --}}
                @include('Lots._includes._list')

                {{-- PANNEAU DROIT : DASHBOARD & SUIVIS --}}
                @include('Lots._includes._detail')
            </div>

        </div>
    </div>

    {{-- MODAL : CREATION DE LOT --}}
    @include('Lots._includes._modal')

    {{-- Styles Mutualisés --}}
    <style>
        .border-left-primary {
            border-left: 4px solid #007bff !important;
        }

        .font-sm {
            font-size: 13px !important;
        }

        .font-xs {
            font-size: 11px !important;
            letter-spacing: 0.5px;
        }

        .btn-xs {
            padding: 1px 5px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
        }

        .shadow-xs {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection
