<?php

namespace App\Http\Controllers;

use App\Models\Accreditation;
use Illuminate\Http\Request;

class AccreditationController extends Controller
{
    public function index()
    {
        $items = Accreditation::orderBy('issued_at','desc')->paginate(20);
        return view('admin.accreditations.index', compact('items'));
    }

    public function create()
    {
        return view('admin.accreditations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'nullable|string|max:255',
            'issued_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'logo_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        Accreditation::create($data);

        return redirect()->route('admin.accreditations.index')->with('success', 'Accreditation created.');
    }

    public function edit(Accreditation $item)
    {
        return view('admin.accreditations.edit', compact('item'));
    }

    public function update(Request $request, Accreditation $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'nullable|string|max:255',
            'issued_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'logo_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $item->update($data);

        return redirect()->route('admin.accreditations.index')->with('success', 'Accreditation updated.');
    }

    public function destroy(Accreditation $item)
    {
        $item->delete();
        return redirect()->route('admin.accreditations.index')->with('success', 'Accreditation removed.');
    }
}
