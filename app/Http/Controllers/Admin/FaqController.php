<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        return view('admin.faqs.index', ['faqs' => Faq::orderBy('sort')->paginate(20)]);
    }

    public function create() { return view('admin.faqs.create'); }

    public function store(Request $request)
    {
        Faq::create($this->validated($request));
        return redirect()->route('admin.faqs.index')->with('success', 'Pregunta agregada.');
    }

    public function edit(Faq $faq) { return view('admin.faqs.edit', compact('faq')); }

    public function update(Request $request, Faq $faq)
    {
        $faq->update($this->validated($request));
        return redirect()->route('admin.faqs.index')->with('success', 'Pregunta actualizada.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return back()->with('success', 'Pregunta eliminada.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer'   => ['required', 'string'],
            'sort'     => ['nullable', 'integer', 'min:0'],
        ]);
    }
}