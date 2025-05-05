<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);

        $users = User::orderBy('created_at','desc')
            ->paginate($perPage);

        return UserResource::collection(($users));
    }

    public function destroy($id) 
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        // Возвращаем успешный ответ
        return response()->json(['message' => 'User successfully deleted'], 200);
    }
}
