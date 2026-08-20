<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Support\ImageNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function index()
    {
        return view('admin.team.index', ['members' => TeamMember::orderBy('sort')->paginate(20)]);
    }

    public function create() { return view('admin.team.create'); }

    public function store(Request $request)
    {
        TeamMember::create($this->validated($request));
        return redirect()->route('admin.team.index')->with('success', 'Miembro agregado.');
    }

    public function edit(TeamMember $team) { return view('admin.team.edit', ['member' => $team]); }

    public function update(Request $request, TeamMember $team)
    {
        $team->update($this->validated($request, $team));
        return redirect()->route('admin.team.index')->with('success', 'Miembro actualizado.');
    }

    public function destroy(TeamMember $team)
    {
        if ($team->photo && !str_starts_with($team->photo, 'http')) {
            Storage::disk('public')->delete($team->photo);
        }
        $team->delete();
        return back()->with('success', 'Miembro eliminado.');
    }

    protected function validated(Request $request, ?TeamMember $item = null): array
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:120'],
            'role'  => ['required', 'string', 'max:120'],
            'sort'  => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:20480'],
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('team', 'public');
            $data['photo'] = ImageNormalizer::normalizeStored($path, 1 / 1, 800); // ← cuadrada
        }

        return $data;
    }
}