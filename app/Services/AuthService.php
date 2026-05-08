<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{

    public function login(array $credentials, ?string $ip = null)
    {
        if (!Auth::attempt($credentials)) {
            Log::channel('auth')->warning('Failed login attempt', [
                'email' => $credentials['email'],
                'ip' => $ip,
            ]);

            return null;
        }

        $user = Auth::user();

        Log::channel('auth')->info('User logged in successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $ip,
        ]);

        return [
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ];
    }

    public function register(array $data, ?string $ip = null)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Log::channel('auth')->info('New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $ip,
        ]);

        return [
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ];
    }

    public function logout(User $user, ?string $ip = null)
    {
        $user->currentAccessToken()->delete();

        Log::channel('auth')->info('User logged out', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $ip,
        ]);
    }
}
