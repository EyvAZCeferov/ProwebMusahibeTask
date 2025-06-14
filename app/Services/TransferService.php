<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transactions\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class TransferService
{
    private TransactionStatusService $statusService;
    private ExchangeRateService $exchangeRateService;
    public function __construct(TransactionStatusService $statusService, ExchangeRateService $exchangeRateService)
    {
        $this->statusService = $statusService;
        $this->exchangeRateService = $exchangeRateService;
    }

    public function process(Account $from, Account $to, float $amount, string $type): array
    {
        $this->validateTransfer($from, $to, $amount, $type);

        return DB::transaction(function () use ($from, $to, $amount, $type) {
            $fromAccount = Account::lockForUpdate()->find($from->id);
            $toAccount = Account::lockForUpdate()->find($to->id);

            $rate = $this->exchangeRateService->getRate($fromAccount->currency_id, $toAccount->currency_id);
            $convertedAmount = $amount * $rate;

            $fromAccount->decrement('balance', $amount);
            $toAccount->increment('balance', round($convertedAmount, 2));

            $transaction_status = $this->type_process_status($type);
            $responseText1 = 'Hesaba köçürmə:';
            $outgoingTx = $this->createTransaction(
                $fromAccount,
                -$amount,
                $this->statusService->getStatusId($transaction_status),
                $responseText1 . ' ' . $toAccount->code,
                ['counterparty_account_id' => $toAccount->id]
            );

            $responseText2 = 'Hesaba mədaxil:';
            $incomingTx = $this->createTransaction(
                $toAccount,
                $convertedAmount,
                $this->statusService->getStatusId($transaction_status),
                $responseText2 . ' ' . $fromAccount->code,
                ['counterparty_account_id' => $fromAccount->id]
            );

            return ['from_transaction' => $outgoingTx, 'to_transaction' => $incomingTx];
        });
    }

    private function type_process_status($type)
    {
        switch ($type) {
            case ('self'):
                return 'self_transfer';
                break;
            case ("external"):
                return "external_transfer";
                break;
            default:
                return "self_transfer";
                break;
        }
    }

    private function validateTransfer(Account $from, Account $to, float $amount, string $type): void
    {
        if ($from->id === $to->id) {
            $responseText = 'Hesablar fərqli olmalıdır.';
            throw new Exception($responseText);
        }

        if ($type === 'self' && $from->user_id !== $to->user_id) {
            $responseText = 'Bu əməliyyat yalnız öz hesablarınız arasında mümkündür.';
            throw new Exception($responseText);
        }

        if ($type === 'external' && $from->user_id === $to->user_id) {
            $responseText = 'Öz hesabınıza köçürmə üçün "self-transfer" endpointindən istifadə edin.';
            throw new Exception($responseText);
        }

        if ($from->balance < $amount) {
            throw new Exception('insufficient_funds');
        }
    }

    private function createTransaction(Account $account, float $amount, int $statusId, string $notes, array $additionalData = []): Transaction
    {
        return Transaction::create([
            'user_id' => $account->user_id,
            'account_id' => $account->id,
            'amount' => $amount,
            'transaction_status_id' => $statusId,
            'notes' => $notes,
            'additional_data' => $additionalData,
        ]);
    }
}
