<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('starts_at','desc')->paginate(20);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:events,slug',
            'content' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'Event created.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:events,slug,' . $event->id,
            'content' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event removed.');
    }
}
