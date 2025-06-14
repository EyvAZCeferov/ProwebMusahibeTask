<?php

namespace App\Http\Requests\Account;

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
        return [
            'currency_id' => 'sometimes|integer|exists:currencies,id',
            'status' => 'sometimes|boolean',
            'start_date' => 'sometimes|date_format:d.m.Y',
            'end_date' => [
                'sometimes',
                'date_format:d.m.Y',
                function ($attribute, $value, $fail) {
                    if ($this->filled('start_date')) {
                        try {
                            $startDate = Carbon::createFromFormat('d.m.Y', $this->start_date);
                            $endDate = Carbon::createFromFormat('d.m.Y', $value);

                            if ($endDate->lt($startDate)) {
                                $tr = $this->translationService->get('end_date start_date-dən sonra və ya bərabər olmalıdır.');
                                $fail($tr);
                            }
                        } catch (\Exception $e) {
                            $tr = $this->translationService->get('Tarix formatı yanlışdır: xx.xx.xxxx olmalıdır');
                            $fail($tr);
                        }
                    }
                },
            ],
            'code' => "sometimes|string|max:36",
            'balance_min' => 'sometimes|numeric|min:0',
            'balance_max' => 'sometimes|numeric|min:0|gte:balance_min',
        ];
    }
}
