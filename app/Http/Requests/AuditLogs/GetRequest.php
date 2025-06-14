<?php

namespace App\Http\Requests\AuditLogs;

use Illuminate\Foundation\Http\FormRequest;

class GetRequest extends FormRequest
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
        return [
            'user_id' => 'sometimes|integer|exists:users,id',
            'ip_address' => 'sometimes|ip',
            'method' => 'sometimes|string|in:GET,POST,PUT,PATCH,DELETE',
            'url' => 'sometimes|string|max:255',
            'status_code' => 'sometimes|integer',
            'min_latency' => 'sometimes|integer|min:0',
            'max_latency' => 'sometimes|integer|min:0|gte:min_latency',
            'browser' => 'sometimes|string',
            'platform' => 'sometimes|string',
        ];
    }
}
