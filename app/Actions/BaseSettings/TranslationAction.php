<?php

namespace App\Actions\BaseSettings;

use App\Actions\CreateAction;
use App\Models\BaseSettings\Translations;
use App\Services\AutoTranslationService;
use Illuminate\Support\Facades\Auth;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TranslationAction implements CreateAction
{
    protected AutoTranslationService $autoTranslator;
    public function __construct(AutoTranslationService $autoTranslator)
    {
        $this->autoTranslator = $autoTranslator;
    }

    public function execute(array $data): Translations
    {
        $key = $data['key'];
        $sourceLocale = config('app.locale', 'az');

        $values = [
            $sourceLocale => $data[$sourceLocale . '_value'] ?? $key,
        ];

        foreach (LaravelLocalization::getSupportedLanguagesKeys() as $targetLocale) {
            if ($targetLocale === $sourceLocale || isset($data[$targetLocale . '_value'])) {
                if (isset($data[$targetLocale . '_value'])) {
                    $values[$targetLocale] = $data[$targetLocale . '_value'];
                }
                continue;
            }

            $translatedText = $this->autoTranslator->translate($values[$sourceLocale], $targetLocale, $sourceLocale);

            if ($translatedText) {
                $values[$targetLocale] = $translatedText;
            }
        }

        $dbData = [
            'key' => $key,
            'value' => $values,
            'user_id' => Auth::id(),
        ];

        return Translations::create($dbData);
    }
}
