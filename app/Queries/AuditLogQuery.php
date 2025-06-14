<?php

namespace App\Queries;

use App\Models\AuditLogs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AuditLogQuery
{
    protected Builder $query;
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->query = AuditLogs::query();
        $this->request = $request;
    }

    public function get(): Builder
    {
        $this->applyUserIdFilter();
        $this->applyIpAddressFilter();
        $this->applyMethodFilter();
        $this->applyUrlFilter();
        $this->applyStatusCodeFilter();
        $this->applyLatencyFilter();
        $this->applyUserAgentFilters();

        $this->query->with('user:id,name');
        $this->query->latest();

        return $this->query;
    }

    protected function applyUserIdFilter(): void
    {
        if ($this->request->filled('user_id')) {
            $this->query->where('user_id', $this->request->input('user_id'));
        }
    }

    protected function applyIpAddressFilter(): void
    {
        if ($this->request->filled('ip_address')) {
            $this->query->where('ip_address', $this->request->input('ip_address'));
        }
    }

    protected function applyMethodFilter(): void
    {
        if ($this->request->filled('method')) {
            $this->query->where('method', strtoupper($this->request->input('method')));
        }
    }

    protected function applyUrlFilter(): void
    {
        if ($this->request->filled('url')) {
            $this->query->where('url', 'like', '%' . $this->request->input('url') . '%');
        }
    }

    protected function applyStatusCodeFilter(): void
    {
        if ($this->request->filled('status_code')) {
            $this->query->where('status_code', $this->request->input('status_code'));
        }
    }

    protected function applyLatencyFilter(): void
    {
        if ($this->request->filled('min_latency')) {
            $this->query->where('latency_ms', '>=', $this->request->input('min_latency'));
        }
        if ($this->request->filled('max_latency')) {
            $this->query->where('latency_ms', '<=', $this->request->input('max_latency'));
        }
    }

    protected function applyUserAgentFilters(): void
    {
        $dbDriver = $this->query->getConnection()->getDriverName();

        if ($this->request->filled('browser')) {
            $browser = $this->request->input('browser');
            if ($dbDriver === 'sqlite') {
                $this->query->where('user_agent', 'like', '%"browser":"' . $browser . '"%');
            } else {
                $this->query->where('user_agent->browser', $browser);
            }
        }

        if ($this->request->filled('platform')) {
            $platform = $this->request->input('platform');
            if ($dbDriver === 'sqlite') {
                $this->query->where('user_agent', 'like', '%"platform":"' . $platform . '"%');
            } else {
                $this->query->where('user_agent->platform', $platform);
            }
        }
    }
}
