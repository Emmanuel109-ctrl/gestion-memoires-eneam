<?php

namespace App\Policies;

use App\Models\Memoire;
use App\Models\User;

class MemoirePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Notice et page de garde.
     */
    public function view(?User $user, Memoire $memoire): bool
    {
        if ($user === null) {
            return $memoire->estPublic();
        }

        if ($memoire->estPersonnel($user) || $memoire->estAuteur($user)) {
            return true;
        }

        if (! $memoire->estValide()) {
            return false;
        }

        return in_array($memoire->confidentialite, ['public', 'interne'], true);
    }

    /**
     * Texte intégral.
     */
    public function read(?User $user, Memoire $memoire): bool
    {
        return $memoire->estConsultablePar($user);
    }

    public function download(?User $user, Memoire $memoire): bool
    {
        return $memoire->estTelechargeablePar($user);
    }

    public function update(User $user, Memoire $memoire): bool
    {
        return $memoire->estAuteur($user) || $user->isAdmin();
    }

    public function validate(User $user, Memoire $memoire): bool
    {
        return $user->isAdmin() || $user->isBibliothecaire();
    }

    public function delete(User $user, Memoire $memoire): bool
    {
        return $user->isAdmin();
    }
}
