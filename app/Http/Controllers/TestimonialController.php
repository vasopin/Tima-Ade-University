<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $items = Testimonial::orderBy('name')->paginate(20);
        return view('admin.testimonials.index', compact('items'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'quote' => 'required|string',
            'photo_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial created.');
    }

    public function edit(Testimonial $item)
    {
        return view('admin.testimonials.edit', compact('item'));
    }

    public function update(Request $request, Testimonial $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'quote' => 'required|string',
            'photo_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $item->update($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated.');
    }

    public function destroy(Testimonial $item)
    {
        $item->delete();
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial removed.');
    }
}
