<!doctype html>
<html class="no-js" lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CƐMARA - {{ $ferme_selectionnee->fer_nom ?? ($fermeActive->fer_nom ?? 'Gestion Avicole') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/cs-skin-elastic.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @stack('styles')
</head>

<body>
    <!-- Panel Gauche (Sidebar Menu) - SEUL ENDROIT POUR SÉLECTIONNER UNE FERME -->
    <aside id="left-panel" class="left-panel">
        @include('SuperAdmin.menu')
    </aside>

    <!-- Panel Droit (Contenu principal) -->
    <div id="right-panel" class="right-panel">
        
        <!-- En-tête (Header) -->
        <header id="header" class="header">
            <div class="top-left">
                <div class="navbar-header">
                    <a class="navbar-brand" href="{{ route('dashboard') }}">
                        <h2 class="logo-text font-20 text-primary font-weight-bold m-0">CƐMARA</h2>
                    </a>
                    <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>
                </div>
            </div>
            <div class="top-right">
                <div class="header-menu">
                    <div class="header-left d-flex align-items-center">
                        <span class="badge badge-primary text-center px-3 py-2 mr-3 font-weight-bold">
                            FERME EN COURS : {{ $ferme_selectionnee->fer_nom ?? session('fer_nom', 'Aucune ferme') }}
                        </span>
                    </div>

                    <div class="user-area dropdown float-right d-flex align-items-center">
                        <span class="mr-3 font-weight-bold text-secondary">
                            @if (auth()->user()->user_etat == 1)
                                <span class="badge badge-danger mr-1">SUPER ADMIN</span>
                            @else
                                <span class="badge badge-info mr-1">ADMIN FERME</span>
                            @endif
                            {{ auth()->user()->name }}
                        </span>

                        <a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if (!empty($ferme_selectionnee) && $ferme_selectionnee->fer_logo)
                                <img src="{{ asset('storage/' . $ferme_selectionnee->fer_logo) }}" alt="Logo {{ $ferme_selectionnee->fer_nom }}" style="height: 38px; width: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #007bff;">
                            @else
                                <div class="bg-primary text-white d-flex align-items-center justify-content-center font-weight-bold" style="height: 38px; width: 38px; border-radius: 50%; font-size: 16px;">
                                    {{ substr($ferme_selectionnee->fer_nom ?? 'C', 0, 1) }}
                                </div>
                            @endif
                        </a>

                        <div class="user-menu dropdown-menu dropdown-menu-right p-2 shadow">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger btn-block">
                                    <i class="fa fa-power-off"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenu de la page -->
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <!-- SECTEUR CENTRAL : FICHE PROFIL DE LA FERME SÉLECTIONNÉE -->
                    <div class="col-lg-8">
                        
                        <!-- Barre supérieure avec nom de la ferme et bouton Ajouter -->
                        <div class="card mb-3 shadow-sm border-0">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <h5 class="text-dark m-0 font-weight-bold">
                                    Ferme : 
                                    @if (isset($ferme_selectionnee))
                                        <span class="badge badge-warning text-dark">{{ $ferme_selectionnee->fer_nom }}</span>
                                    @else
                                        <span class="badge badge-secondary text-white">Aucune ferme sélectionnée</span>
                                    @endif
                                </h5>

                                <a href="{{ route('fermes.save') }}" class="btn btn-sm btn-primary font-weight-bold">
                                    <i class="fa fa-plus"></i> Nouvelle Ferme
                                </a>
                            </div>
                        </div>

                        {{-- FICHE PROFIL --}}
                        @if (isset($ferme_selectionnee))
                            <div class="card shadow border-0">
                                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                                    <strong class="card-title text-primary m-0 font-16">
                                        <i class="fa fa-id-card mr-2"></i> Fiche Profil de la ferme
                                    </strong>
                                </div>

                                <div class="card-body">
                                    <!-- Entête Logo & Nom -->
                                    <div class="row align-items-center mb-4 pb-3 border-bottom">
                                        <div class="col-auto text-center">
                                            @if($ferme_selectionnee->fer_logo)
                                                <img class="rounded-circle img-thumbnail shadow-sm" src="{{ asset('storage/' . $ferme_selectionnee->fer_logo) }}" alt="Logo" style="width: 95px; height: 95px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 95px; height: 95px; font-size: 32px;">
                                                    {{ substr($ferme_selectionnee->fer_nom, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col">
                                            <h4 class="mb-1 text-dark font-weight-bold">{{ $ferme_selectionnee->fer_nom }}</h4>
                                            <p class="text-muted mb-0 font-14">
                                                <i class="fa fa-map-marker text-danger mr-1"></i> Localisation : {{ $ferme_selectionnee->fer_adresse ?: 'Non spécifiée' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Informations Générales -->
                                    <h6 class="font-weight-bold text-primary mb-3">
                                        <i class="fa fa-info-circle mr-1"></i> Informations Générales
                                    </h6>
                                    <div class="row font-14 mb-4">
                                        <div class="col-md-6 mb-2"><strong>Nom :</strong> {{ $ferme_selectionnee->fer_nom }}</div>
                                        <div class="col-md-6 mb-2"><strong>Adresse :</strong> {{ $ferme_selectionnee->fer_adresse ?: 'Non renseignée' }}</div>
                                        <div class="col-md-6 mb-2"><strong>Date de création :</strong> {{ $ferme_selectionnee->created_at ? \Carbon\Carbon::parse($ferme_selectionnee->created_at)->format('d/m/Y') : 'N/A' }}</div>
                                    </div>

                                    <!-- Coordonnées et État -->
                                    <div class="row font-14 mb-4 p-3 bg-light rounded">
                                        <div class="col-md-6 mb-2">
                                            <strong><i class="fa fa-envelope text-muted mr-1"></i> Email :</strong><br>
                                            <span class="text-dark">{{ $ferme_selectionnee->fer_email ?: 'Non renseigné' }}</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong><i class="fa fa-phone text-muted mr-1"></i> Téléphone :</strong><br>
                                            <span class="badge badge-dark font-13">{{ $ferme_selectionnee->fer_telephone ?: 'Non renseigné' }}</span>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <strong><i class="fa fa-toggle-on text-muted mr-1"></i> État de la ferme :</strong>
                                            @if ($ferme_selectionnee->fer_etat == 0)
                                                <span class="badge badge-success font-12">Actif / Opérationnel</span>
                                            @else
                                                <span class="badge badge-danger font-12">Inactif / Suspendu</span>
                                            @endif
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- Actions (Modifier, Activer, Désactiver) -->
                                    <div class="row text-center">
                                        {{-- <div class="col-4 border-right">
                                            <a href="{{ route('fermes.save', $ferme_selectionnee->id) }}" class="btn btn-sm btn-primary btn-block font-weight-bold">
                                                <i class="fa fa-pencil"></i> Modifier
                                            </a>
                                        </div>
                                        <div class="col-4 border-right">
                                            @if($ferme_selectionnee->fer_etat != 0)
                                                <a href="#" class="btn btn-sm btn-warning text-white btn-block font-weight-bold">Activer</a>
                                            @else
                                                <button class="btn btn-sm btn-secondary btn-block" disabled>Activer</button>
                                            @endif
                                        </div> --}}
                                        {{-- <div class="col-4">
                                            @if($ferme_selectionnee->fer_etat == 0)
                                                <a href="#" class="btn btn-sm btn-danger btn-block font-weight-bold">Désactiver</a>
                                            @else
                                                <button class="btn btn-sm btn-secondary btn-block" disabled>Désactiver</button>
                                            @endif
                                        </div> --}}
                                    </div>

                                </div>
                            </div>
                        @else
                            <div class="card bg-light border-dashed shadow-sm text-center py-5">
                                <div class="card-body text-muted">
                                    <i class="fa fa-info-circle fa-3x mb-3 text-primary"></i>
                                    <p class="font-15 font-weight-bold mb-0">
                                        Sélectionnez une ferme dans le menu latéral à gauche pour afficher son profil.
                                    </p>
                                </div>
                            </div>
                        @endif

                    </div>

                    <!-- SECTEUR DROITE : TOTAL ET ACTIONS RAPIDES (INDÉPENDANT DU MENU) -->
                    <div class="col-lg-4">
                        
                        <!-- Résumé Global -->
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-primary text-white">
                                <strong class="card-title m-0"><i class="fa fa-cubes mr-2"></i> Aperçu Global</strong>
                            </div>
                            <div class="card-body text-center p-4">
                                <h2 class="font-weight-bold text-dark mb-1">{{ count($fermes) }}</h2>
                                <span class="text-muted font-14 font-weight-bold text-uppercase">Fermes Enregistrées</span>
                            </div>
                        </div>

                        <!-- Actions Directes sur la Ferme Sélectionnée -->
                        @if (isset($ferme_selectionnee))
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <strong class="card-title text-dark"><i class="fa fa-cogs mr-2"></i> Actions Rapides</strong>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('fermes.save', $ferme_selectionnee->id) }}" class="btn btn-outline-primary btn-block text-left mb-2">
                                        <i class="fa fa-edit mr-2"></i> Modifier cette ferme
                                    </a>
                                    
                                    <a href="{{ route('dashboard') }}" class="btn btn-outline-success btn-block text-left mb-2">
                                        <i class="fa fa-tachometer mr-2"></i> Ouvrir son Menu
                                    </a>

                                    <div class="p-3 bg-light rounded mt-3">
                                        <small class="text-muted d-block">
                                            <i class="fa fa-info-circle mr-1"></i> Pour passer à une autre ferme, utilisez la liste des fermes dans le menu à gauche.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Déconnexion -->
                        <div class="text-center mt-4">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-block font-weight-bold shadow-sm">
                                    <i class="fa fa-power-off mr-1"></i> Déconnexion
                                </button>
                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!--  Chart js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.bundle.min.js"></script>

    <!--Chartist Chart-->
    <script src="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartist-plugin-legend@0.6.2/chartist-plugin-legend.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot-pie@1.0.0/src/jquery.flot.pie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot-spline@0.0.1/js/jquery.flot.spline.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/simpleweather@3.1.0/jquery.simpleWeather.min.js"></script>
    <script src="assets/js/init/weather-init.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/moment@2.22.2/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.js"></script>
    <script src="{{ asset('assets/js/init/fullcalendar-init.js') }}"></script>

    <script src="{{ asset('assets/js/lib/data-table/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/data-table/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/data-table/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/data-table/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/data-table/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/data-table/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/js/lib/data-table/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/data-table/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/data-table/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/js/init/datatables-init.js') }}"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            $('#bootstrap-data-table-export').DataTable();
        });
    </script>

    <!--Local Stuff-->
    <script>
        jQuery(document).ready(function($) {
            "use strict";

            // Pie chart flotPie1
            var piedata = [{
                    label: "Desktop visits",
                    data: [
                        [1, 32]
                    ],
                    color: '#5c6bc0'
                },
                {
                    label: "Tab visits",
                    data: [
                        [1, 33]
                    ],
                    color: '#ef5350'
                },
                {
                    label: "Mobile visits",
                    data: [
                        [1, 35]
                    ],
                    color: '#66bb6a'
                }
            ];

            $.plot('#flotPie1', piedata, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        innerRadius: 0.65,
                        label: {
                            show: true,
                            radius: 2 / 3,
                            threshold: 1
                        },
                        stroke: {
                            width: 0
                        }
                    }
                },
                grid: {
                    hoverable: true,
                    clickable: true
                }
            });
            // Pie chart flotPie1  End
            // cellPaiChart
            var cellPaiChart = [{
                    label: "Direct Sell",
                    data: [
                        [1, 65]
                    ],
                    color: '#5b83de'
                },
                {
                    label: "Channel Sell",
                    data: [
                        [1, 35]creer une affiche pour la fomation en vacance sur informatique en offices 
                    ],
                    color: '#00bfa5'
                }
            ];
            $.plot('#cellPaiChart', cellPaiChart, {
                series: {
                    pie: {
                        show: true,
                        stroke: {
                            width: 0
                        }
                    }
                },
                legend: {
                    show: false
                },
                grid: {
                    hoverable: true,
                    clickable: true
                }

            });
            // cellPaiChart End
            // Line Chart  #flotLine5
            var newCust = [
                [0, 3],
                [1, 5],
                [2, 4],
                [3, 7],
                [4, 9],
                [5, 3],
                [6, 6],
                [7, 4],
                [8, 10]
            ];

            var plot = $.plot($('#flotLine5'), [{
                data: newCust,
                label: 'New Data Flow',
                color: '#fff'
            }], {
                series: {
                    lines: {
                        show: true,
                        lineColor: '#fff',
                        lineWidth: 2
                    },
                    points: {
                        show: true,
                        fill: true,
                        fillColor: "#ffffff",
                        symbol: "circle",
                        radius: 3
                    },
                    shadowSize: 0
                },
                points: {
                    show: true,
                },
                legend: {
                    show: false
                },
                grid: {
                    show: false
                }
            });
            // Line Chart  #flotLine5 End
            // Traffic Chart using chartist
            if ($('#traffic-chart').length) {
                var chart = new Chartist.Line('#traffic-chart', {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    series: [
                        [0, 18000, 35000, 25000, 22000, 0],
                        [0, 33000, 15000, 20000, 15000, 300],
                        [0, 15000, 28000, 15000, 30000, 5000]
                    ]
                }, {
                    low: 0,
                    showArea: true,
                    showLine: false,
                    showPoint: false,
                    fullWidth: true,
                    axisX: {
                        showGrid: true
                    }
                });

                chart.on('draw', function(data) {
                    if (data.type === 'line' || data.type === 'area') {
                        data.element.animate({
                            d: {
                                begin: 2000 * data.index,
                                dur: 2000,
                                from: data.path.clone().scale(1, 0).translate(0, data.chartRect
                                    .height()).stringify(),
                                to: data.path.clone().stringify(),
                                easing: Chartist.Svg.Easing.easeOutQuint
                            }
                        });
                    }
                });
            }
            // Traffic Chart using chartist End
            //Traffic chart chart-js
            if ($('#TrafficChart').length) {
                var ctx = document.getElementById("TrafficChart");
                ctx.height = 150;
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
                        datasets: [{
                                label: "Visit",
                                borderColor: "rgba(4, 73, 203,.09)",
                                borderWidth: "1",
                                backgroundColor: "rgba(4, 73, 203,.5)",
                                data: [0, 2900, 5000, 3300, 6000, 3250, 0]
                            },
                            {
                                label: "Bounce",
                                borderColor: "rgba(245, 23, 66, 0.9)",
                                borderWidth: "1",
                                backgroundColor: "rgba(245, 23, 66,.5)",
                                pointHighlightStroke: "rgba(245, 23, 66,.5)",
                                data: [0, 4200, 4500, 1600, 4200, 1500, 4000]
                            },
                            {
                                label: "Targeted",
                                borderColor: "rgba(40, 169, 46, 0.9)",
                                borderWidth: "1",
                                backgroundColor: "rgba(40, 169, 46, .5)",
                                pointHighlightStroke: "rgba(40, 169, 46,.5)",
                                data: [1000, 5200, 3600, 2600, 4200, 5300, 0]
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        }

                    }
                });
            }
            //Traffic chart chart-js  End
            // Bar Chart #flotBarChart
            $.plot("#flotBarChart", [{
                data: [
                    [0, 18],
                    [2, 8],
                    [4, 5],
                    [6, 13],
                    [8, 5],
                    [10, 7],
                    [12, 4],
                    [14, 6],
                    [16, 15],
                    [18, 9],
                    [20, 17],
                    [22, 7],
                    [24, 4],
                    [26, 9],
                    [28, 11]
                ],
                bars: {
                    show: true,
                    lineWidth: 0,
                    fillColor: '#ffffff8a'
                }
            }], {
                grid: {
                    show: false
                }
            });
            // Bar Chart #flotBarChart End
        });
    </script>

    <script>
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                let venteId = this.dataset.id;

                fetch('/Ventes/Vendre/vente/' + venteId)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('vente-details').innerHTML = html;
                    })
                    .catch(error => {
                        console.error(error);
                        alert('Erreur lors du chargement des détails');
                    });
            });
        });
    </script>

    {{-- <script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.btn-detail').forEach(button => {
        button.addEventListener('click', function () {

            let venteId = this.dataset.id;

            fetch('/Ventes/Vendre/vente/' + venteId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur HTTP');
                    }
                    return response.text();
                })
                .then(html => {
                    document.getElementById('vente-details').innerHTML = html;
                })
                .catch(error => {
                    console.error(error);
                    alert('Impossible de charger les détails de la vente');
                });
        });
    });

});
</script> --}}
</body>
</html>
