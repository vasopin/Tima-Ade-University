<?php

namespace App\Http\Controllers;

use App\Models\LiveClassActivity;
use Illuminate\Http\Request;

class AdminAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->check() && auth()->user()->isAdmin(), 403, 'Administrative privileges required.');
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = LiveClassActivity::with(['actor', 'liveClass'])->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        $activities = $query->paginate(25)->withQueryString();
        $actions = LiveClassActivity::query()->select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.audit-logs.index', compact('activities', 'actions'));
    }
}