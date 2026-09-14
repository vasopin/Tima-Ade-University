<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->check() && auth()->user()->isAdmin(), 403, 'Unauthorized');
            return $next($request);
        });
    }

    public function index()
    {
        $settings = SchoolSetting::all()->keyBy('key');
        $groups   = $settings->groupBy('group');
        return view('settings.index', compact('settings', 'groups'));
    }

    public function update(Request $request)
    {
        // Only admin may update settings
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            SchoolSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('settings.index')
            ->with('success', 'Settings saved successfully!');
    }
}
