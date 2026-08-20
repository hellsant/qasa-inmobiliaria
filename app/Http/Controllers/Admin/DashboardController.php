<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Property;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalProperties'  => Property::count(),
            'activeProperties' => Property::active()->count(),
            'featuredCount'    => Property::featured()->count(),
            'unreadLeads'      => Lead::where('is_read', false)->count(),
            'recentLeads'      => Lead::latest()->take(6)->get(),
            'byOperation'      => Property::selectRaw('operation, count(*) as total')->groupBy('operation')->pluck('total', 'operation'),
        ]);
    }
}