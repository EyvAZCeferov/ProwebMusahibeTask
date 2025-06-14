<?php

namespace App\Http\Requests\Banknote;

use App\Services\TranslationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    protected TranslationService $translationService;
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

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
        return [
            'currency_id' => 'required|integer|exists:currencies,id',
            'name' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('atm_banknotes')->where(function ($query) {
                    return $query->where('currency_id', $this->input('currency_id'));
                })
            ],
            'quantity' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        $responseText = $this->translationService->get('Bu valyuta üçün təyin etdiyiniz əskinaz artıq mövcuddur.');
        return [
            'name.unique' => $responseText,
        ];
    }
}
