<?php

namespace App\Http\Requests\Account;

use App\Rules\UserIsPersonRule;
use App\Services\TranslationService;
use Illuminate\Foundation\Http\FormRequest;

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
        $rules = [
            'currency_id' => 'required|integer|exists:currencies,id',
            'balance' => 'required|numeric|min:0',
            'status' => 'sometimes|boolean',
            'daily_transaction_limit' => 'sometimes|numeric'
        ];

        $user = $this->user();

        if ($user && $user->hasRole('person')) {
            $rules['user_id'] = 'prohibited';
        } else {
            $rules['user_id'] = [
                'required',
                'integer',
                'exists:users,id',
                new UserIsPersonRule($this->translationService),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        $user_prohibited = $this->translationService->get('Siz yalnız özünüz üçün hesab yarada bilərsiniz. İstifadəçi ID-si göndərilməməlidir.');
        $user_required = $this->translationService->get('Başqa bir istifadəçi üçün hesab yaradarkən istifadəçi ID-si mütləqdir.');
        $user_exist = $this->translationService->get('Təyin edilən istifadəçi mövcud deyil.');
        return [
            'user_id.prohibited' => $user_prohibited,
            'user_id.required' => $user_required,
            'user_id.exists' => $user_exist,
        ];
    }
}
