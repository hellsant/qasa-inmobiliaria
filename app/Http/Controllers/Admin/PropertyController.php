<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Zone;
use App\Support\ImageNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $properties = Property::with(['zone', 'images'])
            ->when($request->q, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->op, fn ($q, $op) => $q->where('operation', $op))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('admin.properties.create', ['zones' => Zone::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $property = Property::create($this->validated($request));
        $this->storeImages($property, $request);

        return redirect()->route('admin.properties.edit', $property)
            ->with('success', 'Propiedad creada. Ahora podés gestionar sus fotos y su video.');
    }

    public function edit(Property $property)
    {
        $property->load('images');
        return view('admin.properties.edit', ['property' => $property, 'zones' => Zone::orderBy('name')->get()]);
    }

    public function update(Request $request, Property $property)
    {
        $property->update($this->validated($request));
        $this->storeImages($property, $request);

        return back()->with('success', 'Propiedad actualizada.');
    }

    public function destroy(Property $property)
    {
        foreach ($property->images as $image) {
            if (!str_starts_with($image->path, 'http')) {
                Storage::disk('public')->delete($image->path);
            }
        }
        $property->delete();

        return back()->with('success', 'Propiedad eliminada.');
    }

    /* ---------- Imágenes ---------- */

    public function uploadImages(Request $request, Property $property)
    {
        $request->validate(['images' => ['required', 'array'], 'images.*' => ['image', 'max:20480']]);
        $this->storeImages($property, $request);
        return back()->with('success', 'Fotos subidas y normalizadas (4:3).');
    }

    public function setCover(PropertyImage $image)
    {
        PropertyImage::where('property_id', $image->property_id)->update(['is_cover' => false]);
        $image->update(['is_cover' => true]);
        return back()->with('success', 'Portada actualizada.');
    }

    public function destroyImage(PropertyImage $image)
    {
        if (!str_starts_with($image->path, 'http')) {
            Storage::disk('public')->delete($image->path);
        }
        $image->delete();
        return back()->with('success', 'Foto eliminada.');
    }

    public function toggleFeatured(Property $property)
    {
        $property->update(['is_featured' => !$property->is_featured]);
        return back();
    }

    /* ---------- Helpers ---------- */

    protected function validated(Request $request, ?Property $property = null): array
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'operation'    => ['required', 'in:venta,alquiler,anticretico'],
            'type'         => ['required', 'in:casa,departamento,penthouse,garzonier,condominio,terreno'],
            'zone_id'      => ['nullable', 'exists:zones,id'],
            'price'        => ['required', 'numeric', 'min:0'],
            'currency'     => ['required', 'in:USD,BS'],
            'price_suffix' => ['nullable', 'string', 'max:30'],
            'bedrooms'     => ['nullable', 'integer', 'min:0'],
            'bathrooms'    => ['nullable', 'integer', 'min:0'],
            'area_m2'      => ['nullable', 'numeric', 'min:0'],
            'parking'      => ['nullable', 'integer', 'min:0'],
            'address'      => ['nullable', 'string', 'max:255'],
            'lat'          => ['nullable', 'numeric', 'between:-90,90'],
            'lng'          => ['nullable', 'numeric', 'between:-180,180'],
            'video_url'    => ['nullable', 'string', 'max:255'],
            'social_tiktok'    => ['nullable', 'string', 'max:255'],
            'social_instagram' => ['nullable', 'string', 'max:255'],
            'social_facebook'  => ['nullable', 'string', 'max:255'],
            'social_youtube'   => ['nullable', 'string', 'max:255'],
            'video_file'   => ['nullable', 'file', 'mimes:mp4,webm', 'max:204800'],
            'description'  => ['nullable', 'string'],
            'status'       => ['required', 'in:disponible,reservada,vendida'],
            'is_featured'  => ['boolean'],
            'is_active'    => ['boolean'],
        ], [], [
            'title' => 'título', 'price' => 'precio', 'area_m2' => 'superficie',
        ]);

        $data['features'] = collect(explode("\n", (string) $request->input('features')))
            ->map(fn ($l) => trim($l))->filter()->values()->all();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active');

        /* Video subido → guarda y pisa la URL */
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('videos', 'public');
            $data['video_url'] = asset('storage/' . $path);
        }

        return $data;
    }

    protected function storeImages(Property $property, Request $request): void
    {
        if (!$request->hasFile('images')) {
            return;
        }

        $sort = (int) PropertyImage::where('property_id', $property->id)->max('sort');
        $hasCover = PropertyImage::where('property_id', $property->id)->where('is_cover', true)->exists();

        foreach ($request->file('images') as $i => $file) {
            $path = $file->store("properties/{$property->id}", 'public');
            $path = ImageNormalizer::normalizeStored($path, 4 / 3, 1600);

            PropertyImage::create([
                'property_id' => $property->id,
                'path'        => $path,
                'is_cover'    => !$hasCover && $i === 0,
                'sort'        => ++$sort,
            ]);
        }
    }
}