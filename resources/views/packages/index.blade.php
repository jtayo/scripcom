@extends('layouts.admin')

@section('title', 'Wi-Fi Packages')
@section('page-title', 'Wi-Fi Packages')

@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Wi-Fi Packages
                        <span class="badge bg-secondary-lt ms-2">{{ $packages->total() }}</span>
                    </div>
                    <div class="card-actions d-flex align-items-center gap-2">
                        <form method="GET" action="{{ route('admin.packages.index') }}" class="d-flex gap-1">
                            <div class="input-icon">
                                <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" style="min-width: 220px;"
                                       placeholder="Search packages..."
                                       value="{{ request('search') }}" aria-label="Search packages">
                                @if(request('search'))
                                <a href="{{ route('admin.packages.index') }}" class="input-icon-addon text-decoration-none" title="Clear search">
                                    <i class="ti ti-x"></i>
                                </a>
                                @endif
                            </div>
                            <select name="access_type" class="form-select" style="width: auto;" aria-label="Filter by access type">
                                <option value="">All access types</option>
                                @foreach(['free', 'paid', 'sponsored'] as $type)
                                    <option value="{{ $type }}" @selected(request('access_type') === $type)>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center">
                                <i class="ti ti-filter me-1"></i>Filter
                            </button>
                            @if(request('access_type') || request('search'))
                            <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" title="Clear filters">
                                <i class="ti ti-x"></i>
                            </a>
                            @endif
                        </form>
                        @can('create-package')
                        <a href="{{ route('admin.packages.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-plus me-1"></i>New Package
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-nowrap card-table mb-0">
                        <thead>
                            <tr>
                                <th>Package</th>
                                <th>Access</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Bandwidth</th>
                                <th>Data Limit</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($packages as $package)
                            @php
                                $type = $package->accessType();
                                $statusColor = $package->is_active ? 'success' : 'secondary';
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.packages.show', $package) }}" class="text-body fw-bold text-decoration-none">{{ $package->name }}</a>
                                    <div class="small text-muted">{{ $package->code }}@if($package->organization)? {{ $package->organization->name }}@endif</div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $type->color() }}-lt">{{ $type->label() }}</span>
                                </td>
                                <td class="small text-muted">{{ $package->durationLabel() }}</td>
                                <td class="small text-muted">{{ $package->priceLabel() }}</td>
                                <td class="small text-muted">
                                    @if($package->bandwidth_down_kbps)
                                        {{ number_format($package->bandwidth_down_kbps / 1024, 0) }} Mbps
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    @if($package->data_limit_mb)
                                        {{ number_format($package->data_limit_mb) }} MB
                                    @else
                                        Unlimited
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusColor }}-lt">
                                        <span class="status-dot @if($package->is_active) status-dot-animated @endif me-1"></span>
                                        {{ $package->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.packages.show', $package) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="View">
                                            <i class="ti ti-eye me-1"></i>View
                                        </a>
                                        @can('update-package')
                                        <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Edit">
                                            <i class="ti ti-edit me-1"></i>Edit
                                        </a>
                                        @endcan
                                        @can('delete-package')
                                        <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" class="d-inline" onsubmit="return confirm('Delete this package?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center" title="Delete">
                                                <i class="ti ti-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <div class="my-4">
                                        <i class="ti ti-wifi text-secondary" style="font-size: 2.5rem;"></i>
                                        <div class="mt-2">No Wi-Fi packages found.</div>
                                        @if(request('search') || request('access_type'))
                                        <div class="small text-secondary mt-1">
                                            Try a different filter or <a href="{{ route('admin.packages.index') }}" class="text-primary">clear filters</a>.
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($packages->hasPages())
                <div class="card-footer py-3">
                    {{ $packages->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
