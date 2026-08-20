<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function index()
    {
        return view('admin.milestones.index', ['milestones' => Milestone::orderBy('year')->paginate(20)]);
    }

    public function create() { return view('admin.milestones.create'); }

    public function store(Request $request)
    {
        Milestone::create($request->validate([
            'year' => ['required', 'integer', 'min:1990', 'max:2100'],
            'description' => ['required', 'string', 'max:500'],
        ]));
        return redirect()->route('admin.milestones.index')->with('success', 'Hito agregado.');
    }

    public function edit(Milestone $milestone) { return view('admin.milestones.edit', compact('milestone')); }

    public function update(Request $request, Milestone $milestone)
    {
        $milestone->update($request->validate([
            'year' => ['required', 'integer', 'min:1990', 'max:2100'],
            'description' => ['required', 'string', 'max:500'],
        ]));
        return redirect()->route('admin.milestones.index')->with('success', 'Hito actualizado.');
    }

    public function destroy(Milestone $milestone)
    {
        $milestone->delete();
        return back()->with('success', 'Hito eliminado.');
    }
}