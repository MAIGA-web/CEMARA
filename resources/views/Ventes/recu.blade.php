<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm">
    <page_footer>
        <div style="text-align: center; font-size: 10px; color: #555;">
            <hr style="color: #ccc;">
            @if ($ferme)
                Adresse : {{ $ferme->fer_adresse }} – Tél : {{ $ferme->fer_telephone }} – Email :
                {{ $ferme->fer_email }}
            @elseif(auth()->user()->ferme)
                Adresse : {{ auth()->user()->ferme->fer_adresse }} – Tél : {{ auth()->user()->ferme->fer_telephone }} –
                Email : {{ auth()->user()->ferme->fer_email }}
            @endif
        </div>
    </page_footer>

    <!-- Header -->
    <table style="width: 100%; vertical-align: middle; border-collapse: collapse;">
        <tr>
            <td style="width: 30%; text-align: left;">
                @if ($ferme && $ferme->fer_logo && file_exists(public_path('storage/' . $ferme->fer_logo)))
                    <img src="{{ public_path('storage/' . $ferme->fer_logo) }}" alt="Logo"
                        style="width: 80px; height: auto;">
                @else
                    <div
                        style="width: 80px; height: 80px; border: 1px dashed #ccc; text-align: center; line-height: 80px; color: #aaa; font-size: 12px;">
                        Sans Logo</div>
                @endif
            </td>

            <td style="width: 70%; text-align: right;">
                <h1 style="color: #007bff; margin: 0 0 5px 0; font-size: 24px;">
                    @if (auth()->user()->user_etat == 1)
                        {{ session('fer_nom', 'Administration Globale') }}
                    @else
                        {{ auth()->user()->ferme->fer_nom }}
                    @endif
                </h1>
                <span style="font-size: 14px; color: #555;">
                    À {{ auth()->user()->ferme->fer_adresse ?? 'Adresse non spécifiée' }}
                </span>
            </td>
        </tr>
    </table>
    <hr>

    <div style="text-align: center; margin-top: 30px;">
        <h2 style="text-decoration: none; color: #333;">Reçu de Paiement</h2>
    </div>

    <!-- Infos Client -->
    <div style="margin-top: 20px; line-height: 1.5;">
        <b style="color: #007bff;">Date :</b> {{ now()->format('d/m/Y à H:i') }}<br>
        <b style="color: #007bff;">Client :</b> {{ $vente->client->cl_nom }}
        {{ $vente->client->cl_prenom }}<br>
        <b style="color: #007bff;">Téléphone :</b> {{ $vente->client->cl_tel ?? 'N/A' }}<br>
        <b style="color: #007bff;">Numéro de reçu :</b> # {{ str_pad($vente->id, 6, '0', STR_PAD_LEFT) }}
    </div>

    <!-- Tableau des Produits -->
    <table style="width: 90%; margin-top: 30px; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #007bff; color: white; text-align: center;">
                <th style="width: 10%; border: 1px solid #ccc; padding: 10px;">N°</th>
                <th style="width: 20%; border: 1px solid #ccc; padding: 10px;">Produit</th>
                <th style="width: 20%; border: 1px solid #ccc; padding: 10px;">Type</th>
                <th style="width: 15%; border: 1px solid #ccc; padding: 10px;">Quantité</th>
                <th style="width: 25%; border: 1px solid #ccc; padding: 10px;">Prix Unitaire</th>
                <th style="width: 25%; border: 1px solid #ccc; padding: 10px;">Montant</th>
            </tr>
        </thead>
        <tbody>
            @php $totalVente = 0; @endphp
            @foreach ($produits as $index => $p)
                @php $totalVente += ($p->vdr_pu * $p->vdr_qte); @endphp
                <tr style="text-align: center;">
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->produit->pro_nom }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->produit->pro_type }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $p->vdr_qte }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ number_format($p->vdr_pu, 0, ',', ' ') }} FCFA
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">
                        {{ number_format($p->vdr_qte * $p->vdr_pu, 0, ',', ' ') }} FCFA</td>

                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totaux -->
    <div style="margin-top: 20px; text-align: right; width: 100%;">
        <table style="width: 40%; margin-left: 60%;">
            <tr>
                <td style="padding: 5px; text-align: right;"><b>Montant Total :</b></td>
                <td style="padding: 5px; text-align: right;">{{ number_format($totalVente, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td style="padding: 5px; text-align: right;"><b>Montant Payé :</b></td>
                <td style="padding: 5px; text-align: right;">
                    {{ number_format($totalPaye, 0, ',', ' ') }} FCFA
                </td>
            </tr>
            <tr>
                <td style="padding: 5px; text-align: right;"><b>Reste à Payer :</b></td>
                @php $reste = $totalVente - \App\Models\Paiement::where('vte_id', $vente->id)->sum('pa_payer'); @endphp
                <td style="padding: 5px; text-align: right;">{{ number_format(max(0, $reste), 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

</page>
