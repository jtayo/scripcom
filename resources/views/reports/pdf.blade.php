<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $definition['title'] }} — {{ config('app.name') }}</title>
    <style>
        @page {
            margin: 18mm 12mm 16mm 12mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #1a1a1a;
        }

        .header {
            border-bottom: 2px solid #1d2733;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0 0 2px;
            color: #1d2733;
        }

        .header .meta {
            font-size: 8.5px;
            color: #6b7280;
        }

        .filters {
            font-size: 8.5px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .filters b {
            color: #1a1a1a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        thead th {
            background: #1d2733;
            color: #ffffff;
            text-align: left;
            padding: 5px 6px;
            font-size: 8px;
        }

        tbody td {
            padding: 4px 6px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        tbody tr:nth-child(even) {
            background: #f6f7f9;
        }

        .empty {
            text-align: center;
            color: #9ca3af;
            padding: 40px 0;
        }

        .footer {
            margin-top: 16px;
            font-size: 8px;
            color: #9ca3af;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $definition['title'] }}</h1>
        <div class="meta">
            {{ config('app.name') }} &middot; Generated {{ now()->format('M d, Y H:i') }} &middot;
            {{ count($rows) }} records
        </div>
    </div>

    @php $activeFilters = array_filter($filters, fn ($v) => $v !== null && $v !== ''); @endphp
    @if ($activeFilters)
        <div class="filters">
            <b>Filters:</b>
            @foreach ($activeFilters as $key => $value)
                <span>{{ str_replace('_', ' ', $key) }}: {{ $value }}</span>
                @if (! $loop->last)
                    &middot;
                @endif
            @endforeach
        </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach ($definition['columns'] as $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($definition['columns'] as $key => $label)
                        <td>{{ $row[$key] ?? '—' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($definition['columns']) }}" class="empty">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Powered by {{ config('app.name') }}</div>
</body>

</html>
