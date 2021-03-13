<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class PrivilegesController extends Controller
{
    
    public function getPrivilegesForUser(User $user): JsonResponse
    {
        return response()->json($user->privileges);
    }
    
    public function addPrivilegeToUser(User $user, Request $request): JsonResponse
    {
        if (!$request->user()->hasPrivilege('admin:manage_privs')) {
            return response()->json(['message' => 'You do not have permission to manage privileges.'], 401);
        }
        
        if ($user->hasPrivilege($request->input('role_slug'))) {
            return response()->json(['message' => 'User already has that privilege'], 400);
        }
        
        $user->privileges()->create(['slug' => $request->input('role_slug')]);
        
        return response()->json(['message' => 'Privilege added'], 200);
    }
    
    public function deletePrivilegeFromUser(User $user, Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'role_slug' => 'required|exists:privileges,slug'
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            
            return response()->json(['message' => 'Failed to remove privilege', 'errors' => $errors], 400);
        }
        
        
        if (!$request->user()->hasPrivilege('admin:manage_privs')) {
            return response()->json(['message' => 'You do not have permission to manage privileges.'], 401);
        }
        
        if (!$user->hasPrivilege($request->input('role_slug'))) {
            return response()->json(['message' => 'User does not have that privilege'], 400);
        }

        $user->privileges()->where('slug', $request->input('role_slug'));
        
        return response()->json(['message' => 'Privilege removed']);
    }
}
