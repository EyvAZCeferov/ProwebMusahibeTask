<?php

namespace App\Http\Controllers\Api;

use App\Actions\Account\CreateAccountAction;
use App\Actions\Account\UpdateAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\GetRequest;
use App\Http\Requests\Account\StoreRequest;
use App\Http\Requests\Account\UpdateRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Queries\AccountQuery;
use App\Services\TranslationService;

class AccountController extends Controller
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
        $query = (new AccountQuery($request))->get();
        $accounts = $query->paginate(20)->withQueryString();
        return AccountResource::collection($accounts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request, CreateAccountAction $action)
    {
        $banknote = $action->execute($request->validated());
        return new AccountResource($banknote->load(['currency', 'user', 'creator']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        return new AccountResource($account->load(['currency', 'user', 'creator']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Account $account, UpdateAccountAction $action)
    {
        $data = $action->execute($account, $request->validated());
        return new AccountResource($data->load(['currency', 'user', 'creator']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        $account->delete();
        $responseText = $this->translationService->get('Silindi');
        return response()->json(['status' => true, 'message' => $responseText]);
    }
}
