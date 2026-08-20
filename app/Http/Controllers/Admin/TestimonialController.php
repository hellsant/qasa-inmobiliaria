<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Support\ImageNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index()
    {
        return view('admin.testimonials.index', ['testimonials' => Testimonial::orderBy('sort')->paginate(20)]);
    }

    public function create() { return view('admin.testimonials.create'); }

    public function store(Request $request)
    {
        Testimonial::create($this->validated($request));
        return redirect()->route('admin.testimonials.index')->with('success', 'Historia creada.');
    }

    public function edit(Testimonial $testimonial) { return view('admin.testimonials.edit', compact('testimonial')); }

    public function update(Request $request, Testimonial $testimonial)
    {
        $testimonial->update($this->validated($request, $testimonial));
        return redirect()->route('admin.testimonials.index')->with('success', 'Historia actualizada.');
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->photo && !str_starts_with($testimonial->photo, 'http')) {
            Storage::disk('public')->delete($testimonial->photo);
        }
        $testimonial->delete();
        return back()->with('success', 'Historia eliminada.');
    }

    protected function validated(Request $request, ?Testimonial $item = null): array
    {
        $data = $request->validate([
            'quote'     => ['required', 'string'],
            'author'    => ['required', 'string', 'max:120'],
            'operation' => ['nullable', 'string', 'max:60'],
            'location'  => ['nullable', 'string', 'max:120'],
            'sort'      => ['nullable', 'integer', 'min:0'],
            'photo'     => ['nullable', 'image', 'max:20480'],
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('testimonials', 'public');
            $data['photo'] = ImageNormalizer::normalizeStored($path, 1 / 1, 400); // ← cuadrada (avatar)
        }

        return $data;
    }
}