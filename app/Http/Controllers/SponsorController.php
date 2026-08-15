<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class SponsorController extends Controller
{
    public function index(Request $request): View
    {
        $sponsors = Sponsor::query()
            ->withCount(['sponsorships', 'campaigns'])
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('sponsors.index', compact('sponsors'));
    }

    public function create(): View
    {
        return view('sponsors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $sponsor = Sponsor::create(array_merge($data, [
            'slug' => Str::slug($data['name']) . '-' . Str::lower(Str::random(4)),
        ]));

        return redirect()
            ->route('admin.sponsors.index')
            ->with('success', "Sponsor {$sponsor->name} created.");
    }

    public function show(Sponsor $sponsor): View
    {
        $sponsor->loadCount(['sponsorships', 'campaigns']);

        $stats = [
            'total_sessions' => $sponsor->sponsorships()->sum('quantity_used'),
            'revenue' => $sponsor->sponsorships()->sum('total_amount'),
            'active_sponsorships' => $sponsor->sponsorships()->where('status', 'active')->count(),
        ];

        return view('sponsors.show', compact('sponsor', 'stats'));
    }

    public function edit(Sponsor $sponsor): View
    {
        return view('sponsors.edit', compact('sponsor'));
    }

    public function update(Request $request, Sponsor $sponsor): RedirectResponse
    {
        $data = $this->validated($request);

        $sponsor->update($data);

        return redirect()
            ->route('admin.sponsors.index')
            ->with('success', "Sponsor {$sponsor->name} updated.");
    }

    public function destroy(Sponsor $sponsor): RedirectResponse
    {
        $name = $sponsor->name;
        $sponsor->delete();

        return redirect()
            ->route('admin.sponsors.index')
            ->with('success', "Sponsor {$name} deleted.");
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
