<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\GeneratesUniqueUidTrait;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    use GeneratesUniqueUidTrait;

    public function login()
    {
        $username = $this->generateUniqueUid(User::class);
        $user = User::query()->create([
            'username' => $username,
        ]);

        return $this->success([
            'username' => $user->username,
            'token' => $user->createToken(
                name: 'token_base_name',
                expiresAt: now()->addMinutes(config('sanctum.expiration')))
                ->plainTextToken,
            'expiration' => (config('sanctum.expiration')/24/60) . ' days',
        ]);
    }
}
