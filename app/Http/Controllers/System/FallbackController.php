<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class FallbackController extends Controller
{
    protected TranslationService $translationService;
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }
    public function index()
    {
        return view("system.fallback");
    }
    public function indexApi(Request $request)
    {
        $responseText = $this->translationService->get('API yolu tapılmadı');
        return response()->json([
            'success' => false,
            'message' => $responseText,
        ], 404);
    }
}
