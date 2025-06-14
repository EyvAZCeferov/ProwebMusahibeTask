<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transactions\DeleteRequest;
use App\Http\Requests\Transactions\GetRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transactions\Transaction;
use App\Queries\TransactionQuery;
use App\Services\TranslationService;

class TransactionController extends Controller
{
    protected TranslationService $translationService;
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(GetRequest $request)
    {
        $query = (new TransactionQuery($request))->get();
        $accounts = $query->paginate(20)->withQueryString();
        return TransactionResource::collection($accounts);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction->load(['user', 'account.currency', 'transaction_status', 'transaction_details.atmBanknote']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteRequest $request, Transaction $transaction)
    {
        $transaction->delete();
        $responseText = $this->translationService->get('Silindi');
        return response()->json(['status' => true, 'message' => $responseText]);
    }
}
