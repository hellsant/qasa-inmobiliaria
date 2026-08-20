<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadStoreController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'kind'          => ['required', 'in:contacto,tasacion'],
            'name'          => ['required', 'string', 'max:120'],
            'phone'         => ['nullable', 'string', 'max:40'],
            'interest'      => ['nullable', 'string', 'max:80'],
            'zone'          => ['nullable', 'string', 'max:80'],
            'property_type' => ['nullable', 'string', 'max:80'],
            'operation'     => ['nullable', 'string', 'max:80'],
            'area_m2'       => ['nullable', 'numeric'],
            'message'       => ['nullable', 'string', 'max:2000'],
        ], [], [
            'name' => 'nombre', 'phone' => 'teléfono',
        ]);

        Lead::create($data);

        return back()->with('success', '¡Gracias! Un asesor te contacta en menos de 24 h.');
    }
}