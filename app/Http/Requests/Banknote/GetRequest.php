<?php

namespace App\Http\Requests\Banknote;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class GetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency_id' => 'sometimes|integer|exists:currencies,id',
            'status' => 'sometimes|boolean',
            'name' => 'sometimes|integer',
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
            'min_transactions_count' => 'sometimes|integer|min:1',
            'min_quantity_dispensed' => 'sometimes|integer|min:1',
        ];
    }
}
