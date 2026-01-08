<?php

namespace App\Policies;

use App\Models\KpEvaluation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class KpEvaluationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KpEvaluation $kpEvaluation): bool
    {
        if ($user->isAdminOrKaprodi()) {
            return true;
        }

        if ($user->isDosen()) {
            return $kpEvaluation->student->dosen_id === $user->id;
        }

        if ($user->isPembimbingLapangan()) {
            return $kpEvaluation->student->internship?->pembimbing_lapangan_id === $user->id;
        }

        if ($user->isMahasiswa()) {
            return $kpEvaluation->student->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isPembimbingLapangan();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KpEvaluation $kpEvaluation): bool
    {
        if ($user->isAdminOrKaprodi()) {
            return true;
        }

        return $kpEvaluation->evaluator_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KpEvaluation $kpEvaluation): bool
    {
        if ($user->isAdminOrKaprodi()) {
            return true;
        }

        return $kpEvaluation->evaluator_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, KpEvaluation $kpEvaluation): bool
    {
        return $user->isAdminOrKaprodi();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, KpEvaluation $kpEvaluation): bool
    {
        return $user->isAdminOrKaprodi();
    }
}
