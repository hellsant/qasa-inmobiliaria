<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::latest()
            ->when($request->kind, fn ($q, $k) => $q->where('kind', $k))
            ->paginate(15)
            ->withQueryString();

        return view('admin.leads.index', compact('leads'));
    }

    public function toggleRead(Lead $lead)
    {
        $lead->update(['is_read' => !$lead->is_read]);
        return back();
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return back()->with('success', 'Mensaje eliminado.');
    }
}