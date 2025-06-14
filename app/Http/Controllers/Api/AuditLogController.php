<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuditLogs\GetRequest;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLogs;
use App\Queries\AuditLogQuery;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetRequest $request)
    {
        $query = (new AuditLogQuery($request))->get();
        $logs = $query->paginate(25)->withQueryString();

        return AuditLogResource::collection($logs);
    }

    /**
     * Display the specified resource.
     */
    public function show(AuditLogs $audit_log)
    {
        $audit_log->load('user:id,name');

        return new AuditLogResource($audit_log);
    }
}
