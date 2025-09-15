<?php

namespace App\Repositories;
use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

     public function saveResetCode(User $user, string $code, int $minutes = 15): void
    {
        $user->reset_code = $code;
        $user->reset_code_expires_at = Carbon::now()->addMinutes($minutes);
        $user->save();
    }
}