<?php

namespace App\Policies;

use App\Models\Transactions\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        // Əgər istifadəçi superadmindirsə, heç bir başqa yoxlama etmə, icazə ver.
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // Əgər superadmin deyilsə, normal yoxlamaya (məsələn, delete metoduna) keç.
        return null;
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        // Bu metod yalnız o zaman işə düşəcək ki, istifadəçi superadmin OLMASIN.
        // Çünki superadmin üçün yoxlama `before` metodunda bitir.

        $canDelete = $user->can('delete_transaction');
        $isNotPerson = !$user->hasRole('person');

        return $canDelete && $isNotPerson;
    }
}
