@php
    $role = $role ?? null;
    $selected = old('permissions', $role?->permissions->pluck('name')->all() ?? []);
@endphp

@forelse($permissionGroups as $group => $groupPermissions)
<div class="mb-4">
    <div class="text-uppercase small fw-bold text-secondary mb-1">{{ $group }}</div>
    <hr class="mt-1 mb-3">
    <div class="d-flex flex-wrap gap-2">
        @foreach($groupPermissions as $permission)
        <label class="form-check form-check-pill me-0">
            <input class="form-check-input" type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->name }}" @checked(in_array($permission->name, $selected))>
            <span class="form-check-label">{{ $permission->name }}</span>
        </label>
        @endforeach
    </div>
</div>
@empty
<div class="text-muted">No permissions available.</div>
@endforelse
