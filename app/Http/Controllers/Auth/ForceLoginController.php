<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Operator;

class ForceLoginController extends Controller
{
    public function index()
    {
        return view('auth.force_login');
    }

    /**
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $operator = Operator::whereUsername($request->input('username'))->firstOrFail();
        Auth::login($operator);
        return redirect()->route('home');
    }
}
