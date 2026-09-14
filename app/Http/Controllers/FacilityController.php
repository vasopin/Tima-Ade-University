<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->check() && auth()->user()->isAdmin(), 403, 'Administrative privileges required.');
            return $next($request);
        });
    }

    public function index()
    {
        $facilities = Facility::orderBy('title')->paginate(20);
        return view('admin.facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('admin.facilities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:facilities,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        Facility::create($data);

        return redirect()->route('admin.facilities.index')->with('success', 'Facility created.');
    }

    public function edit(Facility $facility)
    {
        return view('admin.facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:facilities,slug,' . $facility->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $facility->update($data);

        return redirect()->route('admin.facilities.index')->with('success', 'Facility updated.');
    }

    public function destroy(Facility $facility)
    {
        $facility->delete();
        return redirect()->route('admin.facilities.index')->with('success', 'Facility removed.');
    }
}
