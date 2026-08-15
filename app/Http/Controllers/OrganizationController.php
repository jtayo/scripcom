<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(Request $request): View
    {
        $organizations = Organization::query()
            ->withCount(['users', 'hotspots', 'campaigns'])
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('organizations.index', compact('organizations'));
    }

    public function create(): View
    {
        return view('organizations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $organization = Organization::create(array_merge($data, [
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(4)),
        ]));

        return redirect()
            ->route('admin.organizations.index')
            ->with('success', "Organization {$organization->name} created.");
    }

    public function show(Organization $organization): View
    {
        $organization->loadCount(['users', 'hotspots', 'campaigns', 'sponsorships', 'sessions']);

        return view('organizations.show', compact('organization'));
    }

    public function edit(Organization $organization): View
    {
        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $data = $this->validated($request, $organization);

        $organization->update($data);

        return redirect()
            ->route('admin.organizations.index')
            ->with('success', "Organization {$organization->name} updated.");
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $name = $organization->name;
        $organization->delete();

        return redirect()
            ->route('admin.organizations.index')
            ->with('success', "Organization {$name} deleted.");
    }

    private function validated(Request $request, ?Organization $organization = null): array
    {
        $uniqueSlug = $organization
            ? 'unique:organizations,slug,'.$organization->id
            : 'unique:organizations,slug';

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $uniqueSlug],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'county' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'type' => ['nullable', 'string', Rule::in(array_keys(Organization::types()))],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
