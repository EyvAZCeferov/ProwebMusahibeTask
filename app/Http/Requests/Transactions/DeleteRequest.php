<?php

namespace App\Http\Requests\Transactions;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if (!$user->hasRole('person') && $user->can('delete_transaction')) {
            return true;
        }

        return false;
    }

    public function rules(): array
    {
        return [];
    }
}
