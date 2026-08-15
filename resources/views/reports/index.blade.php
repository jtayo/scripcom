@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')
@section('page-subtitle', 'Downloadable, filterable reports of platform activity.')

@section('content')
    <div class="row row-cards">
        @foreach ($definitions as $type => $definition)
            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <span class="avatar avatar-lg bg-primary-lt text-primary me-3">
                                <i class="{{ $definition['icon'] }}"></i>
                            </span>
                            <div>
                                <h3 class="card-title mb-1">{{ $definition['title'] }}</h3>
                                <p class="text-secondary mb-3">{{ $definition['description'] }}</p>
                                <div class="btn-list">
                                    <a href="{{ route('admin.reports.show', $type) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="ti ti-eye me-1"></i>View
                                    </a>
                                    @can('export-reports')
                                        <a href="{{ route('admin.reports.export', ['type' => $type, 'format' => 'xlsx']) }}"
                                            class="btn btn-outline-success btn-sm">
                                            <i class="ti ti-file-spreadsheet me-1"></i>Excel
                                        </a>
                                        <a href="{{ route('admin.reports.export', ['type' => $type, 'format' => 'pdf']) }}"
                                            class="btn btn-outline-danger btn-sm">
                                            <i class="ti ti-file-type-pdf me-1"></i>PDF
                                        </a>
                                        <a href="{{ route('admin.reports.export', ['type' => $type, 'format' => 'csv']) }}"
                                            class="btn btn-outline-secondary btn-sm">
                                            <i class="ti ti-file-text me-1"></i>CSV
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
