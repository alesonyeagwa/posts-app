<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use CloudCreativity\LaravelJsonApi\Http\Requests\CreateResource;

class AuthController extends JsonApiController
{
    //

    public function login(Request $request){
        $credentials =  $request->validate([
            'username' => ['required', 'min:3', 'max:200'],
            'password' => ['required', 'min:8', 'max:50'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = User::where('username', $credentials['username'])->first();
            $user->tokens()->delete();
            $token = $user->createToken('api')->plainTextToken;

            return $this->reply()->meta([
                'token' => $token,
            ]);
        }

        abort(404, 'No account found with the username/password combination.');
    }
}
