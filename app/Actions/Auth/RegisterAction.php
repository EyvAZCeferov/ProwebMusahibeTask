<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterAction
{
    public function execute(array $data): array
    {
        $user = User::create([
            'code' => Str::uuid(),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['user_role']);

        $token = $user->createToken('api-token')->plainTextToken;

        $user = $user->load('roles', 'accounts');

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
