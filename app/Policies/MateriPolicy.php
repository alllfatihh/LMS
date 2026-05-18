<?php

namespace App\Policies;

use App\Models\Materi;
use App\Models\User;

class MateriPolicy
{
    public function kelola(User $user, Materi $materi): bool
    {
        $materi->loadMissing('kelas');

        return $user->role === 'guru'
            && (int) $user->id === (int) $materi->kelas->guru_id;
    }
}
