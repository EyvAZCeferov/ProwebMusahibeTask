<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transfers\ExternalTransferRequest;
use App\Http\Requests\Transfers\SelfTransferRequest;
use App\Models\Account;
use App\Services\TransferService;
use App\Services\TranslationService;
use Exception;
use Illuminate\Http\JsonResponse;

class TransferController extends Controller
{
    private TransferService $transferService;
    private TranslationService $translationService;

    public function __construct(TransferService $transferService, TranslationService $translationService)
    {
        $this->transferService = $transferService;
        $this->translationService = $translationService;
    }

    public function selfTransfer(SelfTransferRequest $request): JsonResponse
    {
        $user = $request->user();
        $fromAccount = Account::where('id', $request->from_account_id)->where('user_id', $user->id)->firstOrFail();
        $toAccount = Account::where('id', $request->to_account_id)->where('user_id', $user->id)->firstOrFail();

        return $this->handleTransfer($fromAccount, $toAccount, $request->amount, 'self');
    }

    public function externalTransfer(ExternalTransferRequest $request): JsonResponse
    {
        $user = $request->user();
        $fromAccount = Account::where('id', $request->from_account_id)->where('user_id', $user->id)->firstOrFail();
        $toAccount = Account::where('code', $request->to_account_code)->firstOrFail();

        return $this->handleTransfer($fromAccount, $toAccount, $request->amount, 'external');
    }

    private function handleTransfer(Account $from, Account $to, float $amount, string $type): JsonResponse
    {
        try {
            $result = $this->transferService->process($from, $to, $amount, $type);
            $responseText = $this->translationService->get('Əməliyyat uğurla tamamlandı');

            return response()->json([
                'message' => $responseText,
                'transactions' => $result
            ]);
        } catch (Exception $e) {
            $errorMessage = $this->translationService->get($e->getMessage());
            return response()->json(['message' => $errorMessage], 422);
        }
    }
}
