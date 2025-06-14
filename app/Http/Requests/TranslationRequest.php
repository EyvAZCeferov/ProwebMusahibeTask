<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TranslationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'key' => "required|string|unique:translations,key",
        ];
        $supportedLocales = LaravelLocalization::getSupportedLanguagesKeys();
        $sourceLocale = config('app.locale', 'az');

        foreach ($supportedLocales as $locale) {
            $rule = 'sometimes|nullable|string';
            if ($locale === $sourceLocale) {
                $rule = 'required|string';
            }
            $rules[$locale . '_value'] = $rule;
        }

        return $rules;
    }
}
