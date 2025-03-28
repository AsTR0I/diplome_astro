<?php

namespace App\Http\Controllers;

use App\Http\Resources\CdrResource;
use App\Models\Cdr;
use Illuminate\Http\Request;

class CdrController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);

        $calls = Cdr::orderBy('calldate', 'desc')
            ->paginate(50);

        return CdrResource::collection(($calls));
    }
}
