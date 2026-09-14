<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NoticeController extends Controller
{
    public function __construct()
    {
        // Only admin may manage notices
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        })->only(['index', 'videos', 'create', 'store', 'edit', 'update', 'destroy']);
    }
    public function index(Request $request)
    {
        $query = Notice::latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $notices = $query->paginate(15)->withQueryString();
        return view('admin.notices.index', compact('notices'));
    }

    public function videos(Request $request)
    {
        $query = Notice::where(function ($query) {
            $query->whereNotNull('video_path')->orWhereNotNull('external_video_url');
        });

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $videos = $query->latest('published_date')->paginate(15)->withQueryString();
        $categories = Notice::where(function ($query) {
                $query->whereNotNull('video_path')->orWhereNotNull('external_video_url');
            })
            ->pluck('category')->unique()->sort()->values();

        return view('admin.notices.videos', compact('videos', 'categories'));
    }

    public function create()
    {
        return view('admin.notices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'category'    => 'required|string|max:100',
            'published_date' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:published_date',
            'status' => 'nullable|in:draft,published,scheduled',
            'audience' => 'nullable|in:everyone,students,teachers,parents,admins',
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime|max:102400',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'attachment' => 'nullable|file|max:20480',
            'external_video_url' => 'nullable|url|max:2048',
            'is_pinned'   => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
        ]);

        Notice::create([
            'title'        => $validated['title'],
            'content'      => $validated['content'],
            'category'     => $validated['category'],
            'published_date' => $validated['published_date'],
            'is_pinned'    => $request->boolean('is_pinned'),
            'is_active'    => $request->has('status') ? $request->input('status') === 'published' : $request->boolean('is_active', true),
            'status'       => $validated['status'] ?? ($request->boolean('is_active', true) ? 'published' : 'draft'),
            'audience'     => $validated['audience'] ?? 'everyone',
            'expires_at'   => $validated['expires_at'] ?? null,
            'video_path'   => $request->hasFile('video') ? $request->file('video')->store('uploads/notices/videos', 'public') : null,
            'video_original_name' => $request->file('video')?->getClientOriginalName(),
            'video_mime_type' => $request->file('video')?->getMimeType(),
            'video_file_size' => $request->file('video')?->getSize(),
            'thumbnail_path' => $request->hasFile('thumbnail') ? $request->file('thumbnail')->store('uploads/notices/thumbnails', 'public') : null,
            'attachment_path' => $request->hasFile('attachment') ? $request->file('attachment')->store('uploads/notices/attachments', 'public') : null,
            'external_video_url' => $validated['external_video_url'] ?? null,
        ]);

        return redirect()->route('admin.notices.index')
            ->with('success', 'Notice published successfully!');
    }

    public function edit(Notice $notice)
    {
        return view('admin.notices.edit', compact('notice'));
    }

    public function update(Request $request, Notice $notice)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'category'     => 'required|string|max:100',
            'published_date' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:published_date',
            'status' => 'nullable|in:draft,published,scheduled',
            'audience' => 'nullable|in:everyone,students,teachers,parents,admins',
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime|max:102400',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'attachment' => 'nullable|file|max:20480',
            'external_video_url' => 'nullable|url|max:2048',
            'is_pinned'    => 'nullable|boolean',
            'is_active'    => 'nullable|boolean',
        ]);

        $notice->update([
            'title'        => $validated['title'],
            'content'      => $validated['content'],
            'category'     => $validated['category'],
            'published_date' => $validated['published_date'],
            'is_pinned'    => $request->boolean('is_pinned'),
            'is_active'    => $request->has('status') ? $request->input('status') === 'published' : $request->boolean('is_active', true),
            'status'       => $validated['status'] ?? ($request->boolean('is_active', true) ? 'published' : 'draft'),
            'audience'     => $validated['audience'] ?? 'everyone',
            'expires_at'   => $validated['expires_at'] ?? null,
            'external_video_url' => $validated['external_video_url'] ?? null,
        ]);

        foreach (['video' => 'video_path', 'thumbnail' => 'thumbnail_path', 'attachment' => 'attachment_path'] as $input => $field) {
            if ($request->hasFile($input)) {
                if ($notice->{$field}) Storage::disk('public')->delete($notice->{$field});
                $notice->{$field} = $request->file($input)->store("uploads/notices/{$input}s", 'public');
                if ($input === 'video') {
                    $notice->video_original_name = $request->file('video')->getClientOriginalName();
                    $notice->video_mime_type = $request->file('video')->getMimeType();
                    $notice->video_file_size = $request->file('video')->getSize();
                }
                $notice->save();
            }
        }

        return redirect()->route('admin.notices.index')
            ->with('success', 'Notice updated successfully!');
    }

    public function destroy(Notice $notice)
    {
        foreach (['video_path', 'thumbnail_path', 'attachment_path'] as $field) {
            if ($notice->{$field}) Storage::disk('public')->delete($notice->{$field});
        }
        $notice->delete();
        return redirect()->route('admin.notices.index')
            ->with('success', 'Notice deleted.');
    }
}
