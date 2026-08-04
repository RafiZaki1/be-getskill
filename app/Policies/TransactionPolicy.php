<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Tentukan apakah user boleh melihat transaksi ini
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $transaction->user_id === $user->id;
    }

    /**
     * Tentukan apakah user boleh membatalkan transaksi ini
     */
    public function cancel(User $user, Transaction $transaction): bool
    {
        return $transaction->user_id === $user->id;
    }

    /**
     * Tentukan apakah user boleh cek status transaksi ini
     */
    public function checkStatus(User $user, Transaction $transaction): bool
    {
        return $transaction->user_id === $user->id;
    }
}
