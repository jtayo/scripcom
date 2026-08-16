@extends('layouts.admin')

@section('title', 'Audit Log #'.$auditLog->id)
@section('page-title', 'Audit Log Details')
@section('page-subtitle', 'Recorded '.($auditLog->created_at?->format('M d, Y H:i:s') ?? '—'))

@php
    $old = $auditLog->old_values ?? [];
    $new = $auditLog->new_values ?? [];
    $changedKeys = array_keys($new);
    $allKeys = collect(array_keys($old))->merge($changedKeys)->unique()->values();
@endphp

@section('content')
    <div class="row row-cards">
        <div class="col-12 col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title mb-0">Details</h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-4">Action</dt>
                        <dd class="col-8">
                            @php
                                $color = match ($auditLog->action) {
                                    'created' => 'success',
                                    'updated' => 'primary',
                                    'deleted', 'force-deleted' => 'danger',
                                    'restored' => 'warning',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $color }}-lt">{{ $auditLog->actionLabel() }}</span>
                        </dd>
                        <dt class="col-4">Entity</dt>
                        <dd class="col-8">{{ $auditLog->entityLabel() }}</dd>
                        @if($auditLog->entity_id)
                        <dt class="col-4">Entity ID</dt>
                        <dd class="col-8">#{{ $auditLog->entity_id }}</dd>
                        @endif
                        <dt class="col-4">Actor</dt>
                        <dd class="col-8">
                            @if($auditLog->user)
                                {{ $auditLog->user->name }}
                                <div class="small text-muted">{{ $auditLog->user->email }}</div>
                            @else
                                <span class="text-muted">System / unauthenticated</span>
                            @endif
                        </dd>
                        <dt class="col-4">IP Address</dt>
                        <dd class="col-8">{{ $auditLog->ip_address ?? '—' }}</dd>
                        <dt class="col-4">User Agent</dt>
                        <dd class="col-8"><span class="text-break small">{{ $auditLog->user_agent ?? '—' }}</span></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title mb-0">Changed Values</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Attribute</th>
                                <th>Previous</th>
                                <th>New</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allKeys as $key)
                                <tr>
                                    <td>
                                        <code>{{ $key }}</code>
                                        @if(in_array($key, $changedKeys, true))
                                            <span class="badge bg-primary-lt ms-1">changed</span>
                                        @endif
                                    </td>
                                    <td class="text-break">
                                        @if(array_key_exists($key, $old) && $old[$key] !== null)
                                            @if(is_array($old[$key]))
                                                <pre class="mb-0 small">{{ json_encode($old[$key], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            @else
                                                {{ is_bool($old[$key]) ? ($old[$key] ? 'true' : 'false') : $old[$key] }}
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-break">
                                        @if(array_key_exists($key, $new) && $new[$key] !== null)
                                            @if(is_array($new[$key]))
                                                <pre class="mb-0 small">{{ json_encode($new[$key], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            @else
                                                {{ is_bool($new[$key]) ? ($new[$key] ? 'true' : 'false') : $new[$key] }}
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <div class="my-4">
                                            <i class="ti ti-clipboard text-secondary" style="font-size: 2.5rem;"></i>
                                            <div class="mt-2">No value changes were recorded for this entry.</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                        <i class="ti ti-arrow-left me-1"></i>Back to Audit Logs
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
