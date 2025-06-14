<?php

namespace App\Rules;

use Closure;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Contracts\Validation\ValidationRule;

class UserIsPersonRule implements ValidationRule
{
    protected TranslationService $translationService;
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::find($value);
        if (!$user || !$user->hasRole('person')) {
            $user_prohibited = $this->translationService->get('İstifadəçi şəxs olmalıdır.');
            $fail($user_prohibited);
        }
    }
}
