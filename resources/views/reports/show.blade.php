@extends('layouts.admin')

@section('title', $definition['title'])
@section('page-title', $definition['title'])
@section('page-subtitle', $definition['description'])

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        {{ $definition['title'] }}
                        <span class="badge bg-secondary-lt ms-2">{{ count($rows) }}</span>
                    </div>
                    <div class="card-actions d-flex flex-wrap align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.reports.show', $type) }}"
                            class="d-flex flex-wrap gap-1">
                            @foreach ($definition['filters'] as $key)
                                @if (in_array($key, ['from', 'to'], true))
                                    <input type="date" name="{{ $key }}" class="form-control" style="width: auto;"
                                        value="{{ $filters[$key] ?? '' }}"
                                        aria-label="{{ $key === 'from' ? 'From' : 'To' }}">
                                @else
                                    <select name="{{ $key }}" class="form-select" style="width: auto;"
                                        aria-label="Filter by {{ $key }}">
                                        <option value="">All {{ str_replace('_', ' ', $key) }}</option>
                                        @foreach ($options[$key] ?? [] as $option)
                                            <option value="{{ $option }}"
                                                @selected(($filters[$key] ?? '') === $option)>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            @endforeach
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if (array_filter($filters, fn ($v) => $v !== null && $v !== ''))
                                <a href="{{ route('admin.reports.show', $type) }}"
                                    class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
                                    <i class="ti ti-x"></i>
                                </a>
                            @endif
                        </form>

                        @can('export-reports')
                            @php
                                $query = array_filter($filters, fn ($v) => $v !== null && $v !== '');
                            @endphp
                            <div class="btn-group">
                                <a href="{{ route('admin.reports.export', array_merge(['type' => $type, 'format' => 'xlsx'], $query)) }}"
                                    class="btn btn-outline-success d-inline-flex align-items-center">
                                    <i class="ti ti-file-spreadsheet me-1"></i>Excel
                                </a>
                                <a href="{{ route('admin.reports.export', array_merge(['type' => $type, 'format' => 'pdf'], $query)) }}"
                                    class="btn btn-outline-danger d-inline-flex align-items-center">
                                    <i class="ti ti-file-type-pdf me-1"></i>PDF
                                </a>
                                <a href="{{ route('admin.reports.export', array_merge(['type' => $type, 'format' => 'csv'], $query)) }}"
                                    class="btn btn-outline-secondary d-inline-flex align-items-center">
                                    <i class="ti ti-file-text me-1"></i>CSV
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                @foreach ($definition['columns'] as $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($paginated->items() as $row)
                                <tr>
                                    @foreach ($definition['columns'] as $key => $label)
                                        <td>{{ $row[$key] ?? '—' }}</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($definition['columns']) }}"
                                        class="text-center text-muted py-5">
                                        <div class="my-4">
                                            <i class="ti ti-file-off text-secondary" style="font-size: 2.5rem;"></i>
                                            <div class="mt-2">No records found for this report.</div>
                                            <div class="small text-secondary mt-1">
                                                Try adjusting the filters above.
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($paginated->hasPages())
                    <div class="card-footer py-3 border-top-0">
                        {{ $paginated->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
