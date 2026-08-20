<?php

namespace App\Http\Controllers;

use App\Models\Property;

class PropertyPublicController extends Controller
{
    public function show(Property $property)
    {
        abort_unless($property->is_active, 404);

        $property->load(['zone', 'images']);

        $similar = Property::active()
            ->where('id', '!=', $property->id)
            ->where('operation', $property->operation)
            ->with(['zone', 'images'])
            ->latest()
            ->take(3)
            ->get();

        return view('property.show', compact('property', 'similar'));
    }
}