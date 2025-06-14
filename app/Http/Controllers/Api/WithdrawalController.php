<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WithdrawalRequest;
use App\Models\Account;
use App\Services\TranslationService;
use App\Services\WithdrawalService;

class WithdrawalController extends Controller
{
    protected TranslationService $translationService;
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function __invoke(WithdrawalRequest $request, WithdrawalService $withdrawalService)
    {
        $account = Account::find($request->account_id);
        try {
            $transaction = $withdrawalService->withdraw($account, $request->amount);
            $responseText = $this->translationService->get('Əməliyyat uğurla tamamlandı');
            return response()->json(['message' => $responseText, 'transaction' => $transaction], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
