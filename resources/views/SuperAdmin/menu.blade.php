<nav class="navbar navbar-expand-sm navbar-default">
    <div id="main-menu" class="main-menu collapse navbar-collapse">
        <ul class="nav navbar-nav">
            <li class="menu-title">Fermes</li>

            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="#">
                    <i class="menu-icon fa fa-laptop"></i>Tableau de bord
                </a>
            </li>

            <!-- Liens dynamiques : rechargent la page avec les infos de la ferme choisie -->
            @foreach ($fermes as $f)
                <li class="{{ (isset($ferme_selectionnee) && $ferme_selectionnee->id == $f->id) || request('fer_id') == $f->id ? 'active' : '' }}">
                    <a href="{{ route('SuperAdmin.index', ['fer_id' => $f->id]) }}">
                        <i class="menu-icon fa fa-home"></i>{{ $f->fer_nom }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</nav>