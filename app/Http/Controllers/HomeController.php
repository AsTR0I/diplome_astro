<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Agent as AgentResource;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $agent = auth()->user();

        $bootstrap = json_encode([
            // Текущий пользователь
            'profile' => new AgentResource($agent),
        ]);

        return view('home', compact('bootstrap'));
    }
}
