<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Throwable;
use Illuminate\Support\Facades\Log;

class AutoTranslationService
{
    public function translate(string $text, string $targetLocale, string $sourceLocale): ?string
    {
        return GoogleTranslate::trans($text, $targetLocale, $sourceLocale) ?? $text;
    }
}
