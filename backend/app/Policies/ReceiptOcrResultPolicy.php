<?php

namespace App\Policies;

use App\Models\ReceiptOcrResult;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReceiptOcrResultPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReceiptOcrResult $receiptOcrResult): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ReceiptOcrResult $receiptOcrResult): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReceiptOcrResult $receiptOcrResult): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ReceiptOcrResult $receiptOcrResult): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ReceiptOcrResult $receiptOcrResult): bool
    {
        return false;
    }
}
