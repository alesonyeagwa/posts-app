<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends JsonApiController
{
    public function register(Request $request){
        $user =  $request->validate([
            'name' => ['required', 'min:3', 'max:255'],
            'username' => ['required', 'min:3', 'max:255', Rule::unique('users', 'username')],
            'password' => ['required', 'min:8', 'max:50'],
        ]);

        $user['password'] = Hash::make($user['password']);
        $uu = User::create($user);

        // return response("Account created successfully.");
        // return $this->rep
        // // return $this->reply([
        // //     'message' => "Account created successfully.",
        // // ]);
        return $this->reply()->created($uu);
    }
}
