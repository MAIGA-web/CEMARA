@php
    $menu = [
        'Ventes',
        'Achats',
        'Lots',
        'Productions',
        'Transformations',
        'Clients',
        'Fournisseurs',
        'Veterinaires',
        'Vaccinations',
        'Alimentations',
        'Collections',
        'Pertes',
        'Produits',
        'Poulaillers',
        'Modes',
        'Matieres'
];
    $icon = [
        'fa fa-shopping-basket', // Ventes
        'fa fa-credit-card', // Achats
        'fa fa-cubes', // Lots (Bandes de poussins)
        'fa fa-industry', // Productions (Œufs, viande)
        'fa fa-flask', // Transformations (ex: Aliments millésimés/mélanges)
        'fa fa-users', // Clients
        'fa fa-truck', // Fournisseurs
        'fa fa-user-md', // Veterinaires
        'fa fa-shield', // Vaccinations (Protection/Santé)
        'fa fa-cutlery', // Alimentations (Silo / Consommation)
        'fa fa-archive', // Collections (Collecte des œufs)
        'fa fa-calendar-times-o', // Pertes (Mortalité / Déchets)
        'fa fa-product-hunt', // Produits
        'fa fa-home', // Poulaillers (Bâtiments)
        'fa fa-cogs', // Modes (Configuration / Règlements)
        'fa fa-leaf' // Matieres (Matières premières / Intrants)
    ];

    $menu_gardien = [
        'Lots',
        'Productions',
        'Transformations',
        'Vaccinations',
        'Alimentations',
        'Collections',
        'Pertes',
        'Produits',
        'Poulaillers',
        'Matieres'
    ];
    $icon_gardien = [
        'fa fa-cubes', // Lots (Bandes de poussins)
        'fa fa-industry', // Productions (Œufs, viande)
        'fa fa-flask', // Transformations (ex: Aliments millésimés/mélanges)
        'fa fa-shield', // Vaccinations (Protection/Santé)
        'fa fa-cutlery', // Alimentations (Silo / Consommation)
        'fa fa-archive', // Collections (Collecte des œufs)
        'fa fa-calendar-times-o', // Pertes (Mortalité / Déchets)
        'fa fa-product-hunt', // Produits
        'fa fa-home', // Poulaillers (Bâtiments)
        'fa fa-leaf' // Matieres (Matières premières / Intrants)
    ];
@endphp
<nav class="navbar navbar-expand-sm navbar-default">
    <div id="main-menu" class="main-menu collapse navbar-collapse">
        <ul class="nav navbar-nav">
            <li class="active">
                <a href="{{ route('dashboard') }}"><i class="menu-icon fa fa-laptop"></i>{{ $ferme_selectionnee->fer_nom ?? session('fer_nom', 'Aucune ferme') }} </a>
            </li>
            <li class="menu-title"> Menu </li><!-- /.menu-title -->
            {{-- @php
    $selectionMenu=request($menu[$i]);
@endphp --}}
   @if (auth()->user()->user_etat == 4)
            {{-- Boucle pour les éléments standards --}}
            @for ($i = 0; $i < count($menu_gardien); $i++)
                @php
                    $selectionMenu_gardien = $menu_gardien[$i];
                @endphp
                <li>
                    <a href="{{ url('/' . $menu_gardien[$i]) }}">
                        <i class="menu-icon {{ $icon_gardien[$i] }}"></i>
                        {{ $menu_gardien[$i] }}
                    </a>
                </li>
            @endfor
@else
            @for ($i = 0; $i < count($menu); $i++)
                @php
                    $selectionMenu = $menu[$i];
                @endphp
                <li>
                    <a href="{{ url('/' . $menu[$i]) }}">
                        <i class="menu-icon {{ $icon[$i] }}"></i>
                        {{ $menu[$i] }}
                    </a>
                </li>
            @endfor
@endif
            {{-- --- GESTION DES UTILISATEURS (Conditionnelle) --- --}}
            {{-- On affiche "Fermes" uniquement pour le Super Admin (etat 1) --}}
            @if (auth()->user()->user_etat == 1)
                <li>
                    <a href="{{ route('SuperAdmin.index') }}">
                        <i class="menu-icon fa fa-university"></i> Fermes
                    </a>
                </li>
            @endif

            {{-- On affiche "Utilisateurs" pour le Super Admin (1) et l'Admin de Ferme (2) --}}
            @if (auth()->user()->user_etat == 1 || auth()->user()->user_etat == 2)
                <li>
                    <a href="{{ route('Users.index') }}">
                        <i class="menu-icon fa fa-users"></i> Utilisateurs
                    </a>
                </li>
            @endif
        </ul>
    </div><!-- /.navbar-collapse -->
</nav>
