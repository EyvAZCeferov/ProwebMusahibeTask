<?php

namespace App\Http\Requests\Transactions;

use App\Services\TranslationService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class GetRequest extends FormRequest
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
        $dateTimeFormat = 'd.m.Y';
        return [
            'start_date' => "sometimes|date_format:{$dateTimeFormat}",
            'end_date' => "sometimes|date_format:{$dateTimeFormat}|after_or_equal:start_date",

            'user_id' => 'sometimes|integer|exists:users,id',
            'account_id' => 'sometimes|integer|exists:accounts,id',
            'status_id' => 'sometimes|integer|exists:transaction_statuses,id',
            'notes' => "sometimes|string|min:3",
            'amount_min' => 'sometimes|numeric|min:0',
            'amount_max' => 'sometimes|numeric|min:0|gte:amount_min',
            'start_date' => 'sometimes|date_format:d.m.Y',
            'end_date' => 'sometimes|date_format:d.m.Y|after_or_equal:start_date',
            'currency_id' => 'sometimes|integer|exists:currencies,id',
            'banknote_id' => 'sometimes|integer|exists:atm_banknotes,id',
            'counterparty_account_id' => 'sometimes|integer|exists:accounts,id',
        ];
    }
}
