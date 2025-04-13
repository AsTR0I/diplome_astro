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
}
