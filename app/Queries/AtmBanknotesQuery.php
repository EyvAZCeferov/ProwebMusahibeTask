<?php

namespace App\Queries;

use App\Models\AtmBanknote;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtmBanknotesQuery
{
    protected Builder $query;
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->query = AtmBanknote::query();
        $this->request = $request;
    }

    public function get(): Builder
    {
        $this->applyStatusFilter();
        $this->applyCurrencyFilter();
        $this->applyNameFilter();
        $this->applyTransactionFilters();

        $this->query->with(['currency']);
        $this->query->latest();

        return $this->query;
    }

    protected function applyStatusFilter(): void
    {
        if ($this->request->has('status')) {
            $status = filter_var($this->request->input('status'), FILTER_VALIDATE_BOOLEAN);
            $this->query->where('status', $status);
        }
    }

    protected function applyCurrencyFilter(): void
    {
        if ($this->request->has("currency_id")) {
            $this->query->where("currency_id", $this->request->input("currency_id"));
        }
    }

    protected function applyNameFilter(): void
    {
        if ($this->request->has('name')) {
            $this->query->where('name', $this->request->input("name"));
        }
    }

    protected function applyTransactionFilters(): void
    {
        $this->applyMinQuantityDispensedFilter();
        $this->applyMinTransactionsCountFilter();
        $this->applyTransactionDateRangeFilter();
    }

    private function applyMinQuantityDispensedFilter(): void
    {
        if ($this->request->has('min_quantity_dispensed')) {
            $minQuantity = (int)$this->request->input('min_quantity_dispensed');

            $subQuery = DB::table('transaction_details')
                ->selectRaw('sum(quantity)')
                ->whereColumn('transaction_details.atm_banknote_id', 'atm_banknotes.id');

            $this->applyDateFilterToSubQuery($subQuery);

            $this->query->whereRaw(
                sprintf('(%s) >= ?', $subQuery->toSql()),
                array_merge($subQuery->getBindings(), [$minQuantity])
            );
        }
    }

    private function applyMinTransactionsCountFilter(): void
    {
        if ($this->request->has('min_transactions_count')) {
            $minCount = (int)$this->request->input('min_transactions_count');

            $subQuery = DB::table('transaction_details')
                ->selectRaw('count(*)')
                ->whereColumn('transaction_details.atm_banknote_id', 'atm_banknotes.id');

            $this->applyDateFilterToSubQuery($subQuery);

            $this->query->whereRaw(
                sprintf('(%s) >= ?', $subQuery->toSql()),
                array_merge($subQuery->getBindings(), [$minCount])
            );
        }
    }

    private function applyTransactionDateRangeFilter(): void
    {
        if (
            ($this->request->has('start_date') || $this->request->has('end_date')) &&
            !$this->request->hasAny(['min_transactions_count', 'min_quantity_dispensed'])
        ) {
            $this->query->whereHas('transactions.transaction', function (Builder $query) {
                $startDate = $this->request->input('start_date');
                $endDate = $this->request->input('end_date');

                if ($startDate) {
                    $query->whereDate('created_at', '>=', Carbon::createFromFormat('d.m.Y', $startDate));
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', Carbon::createFromFormat('d.m.Y', $endDate));
                }
            });
        }
    }

    private function applyDateFilterToSubQuery(\Illuminate\Database\Query\Builder $query): void
    {
        $startDate = $this->request->input('start_date');
        $endDate = $this->request->input('end_date');

        if ($startDate || $endDate) {
            $query->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id');

            if ($startDate) {
                try {
                    $query->whereDate('transactions.created_at', '>=', Carbon::createFromFormat('d.m.Y', $startDate));
                } catch (\Exception $e) {
                }
            }

            if ($endDate) {
                try {
                    $query->whereDate('transactions.created_at', '<=', Carbon::createFromFormat('d.m.Y', $endDate));
                } catch (\Exception $e) {
                }
            }
        }
    }
}
