<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index()
    {
        return view('admin.zones.index', [
            'zones' => Zone::withCount('properties')->orderBy('group')->orderBy('name')->paginate(20),
        ]);
    }

    public function create() { return view('admin.zones.create'); }

    public function store(Request $request)
    {
        Zone::create($this->validated($request));
        return redirect()->route('admin.zones.index')->with('success', 'Zona creada.');
    }

    public function edit(Zone $zone) { return view('admin.zones.edit', compact('zone')); }

    public function update(Request $request, Zone $zone)
    {
        $zone->update($this->validated($request));
        return redirect()->route('admin.zones.index')->with('success', 'Zona actualizada.');
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();
        return back()->with('success', 'Zona eliminada.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'group'    => ['required', 'in:centro,norte,oeste,sur,valle'],
            'price_m2' => ['nullable', 'numeric', 'min:0'],
            'active'   => ['boolean'],
        ]) + ['active' => $request->boolean('active')];
    }
}