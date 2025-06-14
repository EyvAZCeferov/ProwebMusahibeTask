<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AtmBanknote;
use App\Models\Transactions\Transaction;
use App\Models\Transactions\TransactionDetails;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class WithdrawalService
{
    private TransactionStatusService $statusService;
    protected TranslationService $translationService;

    public function __construct(TransactionStatusService $statusService, TranslationService $translationService)
    {
        $this->statusService = $statusService;
        $this->translationService = $translationService;
    }

    public function withdraw(Account $account, float $amount): Transaction
    {
        try {
            $this->validateWithdrawal($account, $amount);

            $banknotesToDispense = $this->calculateBanknotes($account->currency_id, $amount);

            return $this->executeWithdrawalTransaction($account, $amount, $banknotesToDispense);
        } catch (Exception $e) {
            $this->createFailedTransaction($account, $amount, $e->getMessage());

            throw $e;
        }
    }

    private function validateWithdrawal(Account $account, float $amount): void
    {
        if ($amount <= 0) {
            throw new Exception('amount_too_low');
        }

        if ($account->balance < $amount) {
            throw new Exception('insufficient_funds');
        }

        $todayWithdrawals = Transaction::where('user_id', $account->user_id)
            ->whereDate('created_at', today())
            ->where('transaction_status_id', $this->statusService->getStatusId('complete'))
            ->sum('amount');

        $dailyLimit = $account->settings['daily_transaction_limit'] ?? 1000;
        if (($todayWithdrawals + $amount) > $dailyLimit) {
            throw new Exception('daily_limit_exceeded');
        }
    }

    private function calculateBanknotes(int $currencyId, float $amount): array
    {
        $remainingAmount = $amount;
        $banknotesToDispense = [];

        $availableBanknotes = AtmBanknote::where('currency_id', $currencyId)
            ->where('quantity', '>', 0)
            ->where('status', true)
            ->orderBy('name', 'desc')
            ->get();

        foreach ($availableBanknotes as $banknote) {
            if ($remainingAmount <= 0) break;

            $nominal = $banknote->name;
            $quantityNeeded = floor($remainingAmount / $nominal);
            $quantityToTake = min($quantityNeeded, $banknote->quantity);

            if ($quantityToTake > 0) {
                $banknotesToDispense[$banknote->id] = (int)$quantityToTake;
                $remainingAmount -= $quantityToTake * $nominal;
            }
        }

        if (round($remainingAmount, 2) > 0) {
            throw new Exception('banknote_combination_error');
        }

        return $banknotesToDispense;
    }

    private function executeWithdrawalTransaction(Account $account, float $amount, array $banknotesToDispense): Transaction
    {
        return DB::transaction(function () use ($account, $amount, $banknotesToDispense) {
            $account->lockForUpdate()->decrement('balance', $amount);

            foreach ($banknotesToDispense as $banknoteId => $quantity) {
                AtmBanknote::where('id', $banknoteId)->decrement('quantity', $quantity);
            }

            $responseText = $this->translationService->get('ATM-dən nağdlaşdırma uğurla tamamlandı.');
            $transaction = Transaction::create([
                'user_id' => $account->user_id,
                'account_id' => $account->id,
                'amount' => $amount,
                'transaction_status_id' => $this->statusService->getStatusId('complete'),
                'notes' => $responseText,
            ]);

            $detailsPayload = [];
            foreach ($banknotesToDispense as $banknoteId => $quantity) {
                $detailsPayload[] = [
                    'transaction_id' => $transaction->id,
                    'atm_banknote_id' => $banknoteId,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            TransactionDetails::insert($detailsPayload);

            return $transaction;
        });
    }

    private function createFailedTransaction(Account $account, float $amount, string $reasonCode): void
    {
        try {
            $responseText = $this->translationService->get('Nağdlaşdırma cəhdi uğursuz oldu. Səbəb kodu:') . ' ' . $reasonCode;
            Transaction::create([
                'user_id' => $account->user_id,
                'account_id' => $account->id,
                'amount' => $amount,
                'transaction_status_id' => $this->statusService->getStatusId($reasonCode) ?? $this->statusService->getStatusId('failed'),
                'notes' => $responseText,
            ]);
        } catch (Throwable $e) {
            $responseText = $this->translationService->get('Uğursuz əməliyyat jurnalını yaratmaq alınmadı:') . ' ' . $e->getMessage();
            Log::error($responseText);
        }
    }
}
