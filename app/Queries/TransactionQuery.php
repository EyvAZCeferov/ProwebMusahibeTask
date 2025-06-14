<?php

namespace App\Queries;

use App\Models\Transactions\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionQuery
{
    protected Builder $query;
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->query = Transaction::query();
        $this->request = $request;
    }

    public function get(): Builder
    {
        $this->applyRoleFilter();

        $this->applyStatusFilter();
        $this->applyAmountFilter();
        $this->applyDateFilter();
        $this->applyNotesFilter();

        $this->applyUserFilter();
        $this->applyAccountFilter();
        $this->applyCurrencyFilter();
        $this->applyBanknoteFilter();
        $this->applyCounterpartyAccountFilter();

        $this->query->with(['user', 'account.currency', 'transaction_status', 'transaction_details.atmBanknote']);
        $this->query->latest();

        return $this->query;
    }

    protected function applyRoleFilter(): void
    {
        $user = Auth::user();
        if ($user && $user->hasRole('person')) {
            $this->query->where('user_id', $user->id);
        }
    }

    protected function applyStatusFilter(): void
    {
        if ($this->request->filled('status_id')) {
            $this->query->where('transaction_status_id', $this->request->input('status_id'));
        }
    }

    protected function applyUserFilter(): void
    {
        if ($this->request->filled('user_id')) {
            $this->query->where('user_id', $this->request->input('user_id'));
        }
    }

    protected function applyAccountFilter(): void
    {
        if ($this->request->filled('account_id')) {
            $this->query->where('account_id', $this->request->input('account_id'));
        }
    }

    protected function applyAmountFilter(): void
    {
        if ($this->request->filled('amount_min')) {
            $this->query->where('amount', '>=', $this->request->input('amount_min'));
        }
        if ($this->request->filled('amount_max')) {
            $this->query->where('amount', '<=', $this->request->input('amount_max'));
        }
    }

    protected function applyDateFilter(): void
    {
        $dateTimeFormat = 'd.m.Y';

        if ($this->request->filled('start_date')) {
            try {
                $startDate = Carbon::createFromFormat($dateTimeFormat, $this->request->start_date);
                $this->query->where('created_at', '>=', $startDate);
            } catch (\Exception $e) {
            }
        }

        if ($this->request->filled('end_date')) {
            try {
                $endDate = Carbon::createFromFormat($dateTimeFormat, $this->request->end_date);
                $this->query->where('created_at', '<=', $endDate);
            } catch (\Exception $e) {
            }
        }
    }
    protected function applyNotesFilter(): void
    {
        if ($this->request->filled('notes')) {
            $this->query->where('notes', 'like', '%' . $this->request->input('notes') . '%');
        }
    }

    protected function applyCurrencyFilter(): void
    {
        if ($this->request->filled('currency_id')) {
            $this->query->whereHas('account', function (Builder $query) {
                $query->where('currency_id', $this->request->input('currency_id'));
            });
        }
    }

    protected function applyBanknoteFilter(): void
    {
        if ($this->request->filled('banknote_id')) {
            $this->query->whereHas('transaction_details', function (Builder $query) {
                $query->where('atm_banknote_id', $this->request->input('banknote_id'));
            });
        }
    }

    protected function applyCounterpartyAccountFilter(): void
    {
        if ($this->request->filled('counterparty_account_id')) {
            $accountId = $this->request->input('counterparty_account_id');

            $dbDriver = $this->query->getConnection()->getDriverName();

            if ($dbDriver === 'sqlite') {
                $this->query->where(function ($query) use ($accountId) {
                    $query->where('additional_data', 'like', '%"counterparty_account_id":' . $accountId . '%')
                        ->orWhere('additional_data', 'like', '%"counterparty_account_id": ' . $accountId . '%');
                });
            } else {
                $this->query->where('additional_data->counterparty_account_id', $accountId);
            }
        }
    }
}
