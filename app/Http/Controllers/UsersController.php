<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class UsersController extends Controller
{
    public function update(Request $request): JsonResponse
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
        
        return response()->json(['message' => 'User updated']);
        
    }
    
    public function register(Request $request): JsonResponse
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
        
        return response()->json(['message' => 'User created']);
    }
    
    public function login(Request $request): JsonResponse
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

        $token = auth()->attempt(['username' => $request->input('username'), 'password' => $request->input('password')]);
        if ($token) {
            $user = User::where('username', $request->input('username'))->first();
            if (Hash::needsRehash($user->password)) {
                $user->password = Hash::make($request->input('password'));
                $user->save();
            }
            return response()->json(['token' => $token, 'user' => $user->toArray()]);
        }
        
        return response()->json(['message' => 'Authentication failed'], 401);
    }

    public function logout(Request $request): JsonResponse {
        if ($request->user()) {
            auth()->logout();
        }
        return response()->json(['message' => 'Logged out']);
    }
}
