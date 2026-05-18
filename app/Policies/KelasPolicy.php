<?php

namespace App\Policies;

use App\Models\Kelas;
use App\Models\User;

class KelasPolicy
{
    public function view(User $user, Kelas $kelas): bool
    {
        return $user->role === 'guru' && (int) $user->id === (int) $kelas->guru_id;
    }

    public function update(User $user, Kelas $kelas): bool
    {
        return $this->view($user, $kelas);
    }

    public function delete(User $user, Kelas $kelas): bool
    {
        return $this->view($user, $kelas);
    }
}
