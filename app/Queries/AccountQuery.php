<?php

namespace App\Queries;

use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountQuery
{
    protected Builder $query;
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->query = Account::query();
        $this->request = $request;
    }


    public function get(): Builder
    {
        $this->applyRoleFilter();
        $this->applyStatusFilter();
        $this->applyCurrencyFilter();
        $this->applyBalanceFilter();
        $this->applyCodeFilter();
        $this->applyDateFilter();
        $this->query->with(['currency', 'user', 'creator']);

        $this->query->latest();

        return $this->query;
    }

    protected function applyRoleFilter(): void
    {
        $user = Auth::user();

        if ($user->hasRole('person')) {
            $this->query->where('user_id', $user->id);
        }
    }

    protected function applyCurrencyFilter(): void
    {
        if ($this->request->has("currency_id")) {
            $this->query->where("currency_id", $this->request->input("currency_id"));
        }
    }

    protected function applyBalanceFilter(): void
    {
        if ($this->request->has("balance_min")) {
            $this->query->where("balance", '>=', $this->request->input("balance_min"));
        }

        if ($this->request->has("balance_max")) {
            $this->query->where("balance", '<=', $this->request->input("balance_max"));
        }
    }


    protected function applyStatusFilter(): void
    {
        if ($this->request->has('status')) {
            $status = filter_var($this->request->input('status'), FILTER_VALIDATE_BOOLEAN);
            $this->query->where('status', $status);
        }
    }

    protected function applyCodeFilter(): void
    {
        if ($this->request->has('code')) {
            $this->query->where('code', $this->request->input("code"));
        }
    }

    protected function applyDateFilter(): void
    {
        if ($this->request->has('start_date')) {
            $startDate = Carbon::createFromFormat('d.m.Y', $this->request->input('start_date'))->startOfDay();
            $this->query->where('created_at', '>=', $startDate);
        }

        if ($this->request->has('end_date')) {
            $endDate = Carbon::createFromFormat('d.m.Y', $this->request->input('end_date'))->endOfDay();
            $this->query->where('created_at', '<=', $endDate);
        }
    }
}
