<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Milestone;
use App\Models\Property;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Zone;

class HomeController extends Controller
{
    public function index()
    {
        $properties = Property::active()
            ->with(['zone', 'images'])
            ->orderByDesc('is_featured')
            ->latest()
            ->get();

        $zones = Zone::where('active', true)
            ->withCount(['properties' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('home', [
            'properties' => $properties,
            'featured'   => $properties->where('is_featured', true),
            'zones'      => $zones,
            'team'       => TeamMember::orderBy('sort')->get(),
            'testimonials' => Testimonial::orderBy('sort')->get(),
            'faqs'       => Faq::orderBy('sort')->get(),
            'milestones' => Milestone::orderBy('year')->get(),
        ]);
    }
}