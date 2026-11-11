<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} — IT Manager Pro</title>
    <style>
        @page {
            size: {{ $orientation === 'landscape' ? 'A4 landscape' : 'A4 portrait' }};
            margin: 15mm 12mm 15mm 12mm;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 11px;
            color: #1E293B;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        .header {
            border-bottom: 2px solid #014392;
            padding-bottom: 12px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .app-name {
            font-size: 18px;
            font-weight: 800;
            color: #014392;
            letter-spacing: -0.5px;
        }
        .app-sub {
            font-size: 10px;
            color: #64748B;
            margin-top: 2px;
        }
        .doc-title {
            font-size: 15px;
            font-weight: 700;
            color: #014392;
            margin-top: 6px;
        }
        .meta-box {
            text-align: right;
            font-size: 10px;
            color: #64748B;
        }
        .filters-badge {
            background-color: #F4F7FB;
            border: 1px solid #E2E8F0;
            padding: 6px 10px;
            border-radius: 6px;
            margin-bottom: 14px;
            font-size: 10px;
            color: #475569;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 20px;
        }
        th {
            background-color: #F4F7FB;
            color: #014392;
            font-weight: 700;
            text-align: left;
            padding: 8px 6px;
            border-bottom: 1.5px solid #CBD5E1;
            border-top: 1px solid #E2E8F0;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
        td {
            padding: 6px 6px;
            border-bottom: 1px solid #E2E8F0;
            vertical-align: middle;
        }
        tr:nth-child(even) {
            background-color: #FAFCFF;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #E2E8F0;
            padding-top: 6px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #94A3B8;
        }
        .print-btn {
            position: fixed;
            top: 15px;
            right: 15px;
            background: #014392;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .print-btn:hover {
            background: #0161B9;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Print Action Bar (Hidden when printing/saved as PDF) -->
    <div class="no-print" style="background: #014392; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <strong>Aperçu d'impression PDF — {{ $title }}</strong>
            <span style="font-size: 11px; opacity: 0.8; margin-left: 10px;">Cliquez sur « Imprimer / Enregistrer en PDF » pour générer le document PDF final.</span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="print-btn" style="position: static;">
                Imprimer / Enregistrer en PDF
            </button>
            <button onclick="window.close()" style="background: rgba(255,255,255,0.2); color: white; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer;">
                Fermer
            </button>
        </div>
    </div>

    <!-- Document Header -->
    <div class="header">
        <div>
            <div class="app-name">IT MANAGER PRO</div>
            <div class="app-sub">Gestion du stock de matériel informatique</div>
            <div class="doc-title">{{ $title }}</div>
        </div>
        <div class="meta-box">
            <div>Édité le : <strong>{{ $generatedAt }}</strong></div>
            <div>Total résultats : <strong>{{ count($rows) }}</strong></div>
        </div>
    </div>

    @if (!empty($metadata['filters']))
        <div class="filters-badge">
            <strong>Filtres appliqués :</strong> {{ $metadata['filters'] }}
        </div>
    @endif

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" style="text-align: center; padding: 20px; color: #94A3B8;">
                        Aucun enregistrement trouvé pour ce rapport.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div>IT Manager Pro &bull; Gestion du stock informatique</div>
        <div>Page générée automatiquement &bull; Données certifiées</div>
    </div>

</body>
</html>
