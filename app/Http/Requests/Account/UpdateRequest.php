<?php

namespace App\Http\Requests\Account;

use App\Rules\UserIsPersonRule;
use App\Services\TranslationService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        $rules = [
            'currency_id' => 'prohibited',
            'balance' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|boolean',
            'user_id' => 'prohibited',
            'daily_transaction_limit' => 'sometimes|numeric'
        ];

        return $rules;
    }

    public function messages(): array
    {
        $user_prohibited = $this->translationService->get('Siz yalnız mövcud hesab parametrlərini yeniləyə bilərsiniz. İstifadəçi ID-si göndərilməməlidir.');
        return [
            'user_id.prohibited' => $user_prohibited,
        ];
    }
}
