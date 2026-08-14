@extends('layouts.admin')

@section('title', 'Sponsors')
@section('page-title', 'Sponsors')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.sponsors.index') }}" class="row g-3 align-items-center">
                        <div class="col-12 col-md-5 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text"><svg class="icon text-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg></span>
                                <input type="text" name="search" class="form-control" placeholder="Search sponsors..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-lg-2">
                            <button type="submit" class="btn btn-dark d-inline-flex align-items-center">Filter</button>
                        </div>
                        <div class="col-12 col-lg-6 text-lg-end">
                            @can('create-sponsor')
                            <a href="{{ route('admin.sponsors.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                                <svg class="icon me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                New Sponsor
                            </a>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-nowrap mb-0">
                            <thead class="">
                                <tr>
                                    <th class="border-0 rounded-start">Sponsor</th>
                                    <th class="border-0">Contact</th>
                                    <th class="border-0">Sponsorships</th>
                                    <th class="border-0">Campaigns</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 rounded-end text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sponsors as $sponsor)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm me-3" style="background-image: url('{{ asset('img/team/profile-picture-3.jpg') }}')"></span>
                                            <span class="text-body fw-bold">
                                                <a href="{{ route('admin.sponsors.show', $sponsor) }}">{{ $sponsor->name }}</a>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $sponsor->contact_person ?? '—' }}</div>
                                        <div class="small text-muted">{{ $sponsor->email ?? '' }} {{ $sponsor->phone ?? '' }}</div>
                                    </td>
                                    <td>{{ number_format($sponsor->sponsorships_count) }}</td>
                                    <td>{{ number_format($sponsor->campaigns_count) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $sponsor->is_active ? 'success' : 'secondary' }}">{{ $sponsor->is_active ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.sponsors.show', $sponsor) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">View</a>
                                            @can('update-sponsor')
                                            <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">Edit</a>
                                            @endcan
                                            @can('delete-sponsor')
                                            <form method="POST" action="{{ route('admin.sponsors.destroy', $sponsor) }}" class="d-inline" onsubmit="return confirm('Delete this sponsor?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">Delete</button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-5">No sponsors found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($sponsors->hasPages())
                <div class="card-footer border-0 py-2">
                    {{ $sponsors->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
