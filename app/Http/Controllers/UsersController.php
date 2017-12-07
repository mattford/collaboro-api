<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;

use App\User;

class UsersController extends Controller
{
    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'username' => 'required|exists:users',
                'password' => 'confirmed',
                'email' => 'email|unique:users',
                'name' => 'regex:/[a-zA-Z ]+/',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            
            return response()->json(['message' => 'Failed to update user', 'errors' => $errors], 400);
        }
        
        $loggedInUser = $request->user();
        
        $user = User::where('username', $request->input('username'))->first();
        
        if (
            ($loggedInUser->id !== $user->id && !$user->hasPrivilege('user:update_self')) &&
            !$loggedInUser->hasPrivilege('user:update_other')
        ){
            return response()->json(['message' => 'You do not have permission to update this user.'], 401);
        }
        
        $user->fill($request->all());
        
        $user->save();
        
        return response()->json(['message' => 'User updated'], 200);
        
    }
    
    public function register(Request $request)
    {
        try {
            $this->validate($request, [
                'username' => 'required|alpha_num|min:8|unique:users',
                'password' => 'required|confirmed',
                'email' => 'required|email|unique:users',
                'name' => 'required|regex:/[a-zA-Z ]+/',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            
            return response()->json(['message' => 'Failed to register user.', 'errors' => $errors], 400);
        }

        $user = new User([
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'email' => $request->input('email'),
            'name' => $request->input('name'),
        ]);
        
        $user->save();
        
        return response()->json(['message' => 'User created'], 200);
    }
    
    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'username' => 'required',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            
            return response()->json(['message' => 'Failed to login.', 'errors' => $errors], 400);
        }
        
        $user = User::where('username', $request->input('username'))->first();
        
        if (Hash::check($request->input('password'), $user->password)) {
            if (Hash::needsRehash($user->password)) {
                $user->password = Hash::make($request->input('password'));
                $user->save();
            }
            
            $token = JWTAuth::fromUser($user);
            
            return response()->json(['token' => $token], 200);
        }
        
        return response()->json(['message' => 'Authentication failed'], 401);
    }
}
