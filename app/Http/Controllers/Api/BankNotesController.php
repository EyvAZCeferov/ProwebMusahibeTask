<?php

namespace App\Http\Controllers\Api;

use App\Actions\Banknotes\CreateBanknoteAction;
use App\Actions\Banknotes\UpdateBanknoteAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Banknote\GetRequest;
use App\Http\Requests\Banknote\StoreRequest;
use App\Http\Requests\Banknote\UpdateRequest;
use App\Http\Resources\BanknoteResource;
use App\Models\AtmBanknote;
use App\Queries\AtmBanknotesQuery;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class BankNotesController extends Controller
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
        $query = (new AtmBanknotesQuery($request))->get();
        $banknotes = $query->paginate(20)->withQueryString();

        return BanknoteResource::collection($banknotes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request, CreateBanknoteAction $createBanknoteAction)
    {
        $banknote = $createBanknoteAction->execute($request->validated());
        return new BanknoteResource($banknote->load(['currency', 'user']));
    }

    /**
     * Display the specified resource.
     */
    public function show(AtmBanknote $banknote)
    {
        return new BanknoteResource($banknote->load(['currency', 'user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, AtmBanknote $banknote, UpdateBanknoteAction $updateBanknoteAction)
    {
        $updatedBanknote = $updateBanknoteAction->execute($banknote, $request->validated());
        return new BanknoteResource($updatedBanknote->load(['currency', 'user']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AtmBanknote $banknote)
    {
        $banknote->delete();
        $responseText = $this->translationService->get('Silindi');
        return response()->json(['status' => true, 'message' => $responseText]);
    }
}
