<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{
    public function register(Request $request)
    {

        $validation = Validator::make($request->all() ,
        [
           'name' => 'required|string|max:120|min:3',
           'email' => 'required|string|unique:users,email',
           'cellphone' => 'required|string|unique:users,cellphone',
           'password' => 'required',
           'confirm_password' => 'required|same:password',
        ]);

        if ($validation->fails())
        {
            return $this->ErrorResponse($validation->getMessageBag() , 422);
        }


        $user = User::create(
            [
                'name' => $request->name,
                'email' => $request->email,
                'cellphone' => $request->cellphone,
                'password' => Hash::make($request->password),
            ]
        );

        $token = $user->createToken('fashi')->plainTextToken;

        return $this->SuccessResponse("authentication successful" , 200 ,
        [
            'user' => $user,
            'token' => $token
        ]
        );

    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->all() ,
            [
                'email' => 'required|string',
                'password' => 'required',
            ]);

        if ($validation->fails())
        {
            return $this->ErrorResponse($validation->getMessageBag() , 422);
        }

        $user = User::query()->where('email' , $request->email)->first();

        if (!$user)
        {
            return  $this->ErrorResponse("user with this email not found" , 422);
        }

        if (!Hash::check($request->password , $user->password))
        {
            return  $this->ErrorResponse("wrong password" , 401);
        }


        $token = $user->createToken('fashi')->plainTextToken;

        return $this->SuccessResponse("athurization successful" , 200 ,
            [
                'user' => $user,
                'token' => $token
            ]
        );
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->SuccessResponse("user logout successful" , 200);
    }
}
