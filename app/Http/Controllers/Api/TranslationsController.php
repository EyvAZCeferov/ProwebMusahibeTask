<?php

namespace App\Http\Controllers\Api;

use App\Actions\BaseSettings\TranslationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TranslationRequest;
use App\Http\Resources\TranslationResource;
use App\Models\BaseSettings\Translations;
use App\Services\TranslationService;

class TranslationsController extends Controller
{
    protected TranslationService $translationService;
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Translations::latest()->paginate(20);
        return TranslationResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TranslationRequest $request, TranslationAction $action)
    {
        $tr = $action->execute($request->validated());
        return new TranslationResource($tr);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Translations $translation)
    {
        $translation->delete();
        $responseText = $this->translationService->get('Silindi');
        return response()->json(['status' => true, 'message' => $responseText]);
    }
}
