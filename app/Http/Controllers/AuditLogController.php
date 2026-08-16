<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $auditLogs = AuditLog::query()
            ->with('user:id,name,email')
            ->search($request->search)
            ->when($request->action, fn ($q, $action) => $q->where('action', $action))
            ->when($request->entity_type, fn ($q, $type) => $q->where('entity_type', $type))
            ->when($request->from, fn ($q, $from) => $q->where('created_at', '>=', $from.' 00:00:00'))
            ->when($request->to, fn ($q, $to) => $q->where('created_at', '<=', $to.' 23:59:59'))
            ->latest('created_at')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $actions = AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action');
        $entityTypes = AuditLog::query()
            ->select('entity_type')
            ->whereNotNull('entity_type')
            ->distinct()
            ->orderBy('entity_type')
            ->pluck('entity_type');

        return view('audit-logs.index', compact('auditLogs', 'actions', 'entityTypes'));
    }

    public function show(AuditLog $auditLog): View
    {
        $auditLog->load('user:id,name,email');

        return view('audit-logs.show', compact('auditLog'));
    }
}
