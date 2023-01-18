<?php


namespace App\Services\User;


use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create($data)
    {
        $user = User::where('email', $data['email'])->first();

        if($user) {
            if($user['email'] == $data['email']) {
                return response()->json(['message' => 'Данный email уже занят'], 403);
            }
        }
        else {
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            $token = auth()->tokenById($user->id);
            return response()->json([
                'access_token' => $token,
            ]);
        }
    }
}
