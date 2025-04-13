<?php

namespace App\Http\Controllers;

use App\Http\Resources\SippeerResource;
use App\Sippeer;
use Illuminate\Http\Request;

class SippeerController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $extensions = Sippeer::orderBy('id', 'desc')
            ->paginate(50);
        return SippeerResource::collection($extensions);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Валидация данных
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'secret' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'context' => 'required|string|max:255',
            'nat' => 'required|string|max:255',
            'directmedia' => 'required|string|max:255',
            'ipaddr' => 'nullable|string|ipv4',
            'port' => 'nullable|string',
            'allow' => 'required|string|max:255',
        ]);

        $sippeer = Sippeer::create($validatedData);

        return new SippeerResource($sippeer);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sippeer  $sippeer
     * @return \Illuminate\Http\Response
     */
    public function show(Sippeer $sippeer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sippeer  $sippeer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sippeer = Sippeer::findOrFail($id);
        return new SippeerResource($sippeer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sippeer  $sippeer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'secret' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'context' => 'required|string|max:255',
            'nat' => 'required|string|max:255',
            'directmedia' => 'required|string|max:255',
            'ipaddr' => 'nullable|string|ipv4',
            'port' => 'nullable|string',
            'allow' => 'required|string|max:255',
        ]);

        $sippeer = Sippeer::findOrFail($id);
        $sippeer->update($validatedData);
        return new SippeerResource($sippeer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sippeer  $sippeer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sippeer = Sippeer::find($id);

        $sippeer = Sippeer::findOrFail($id);
         if ($sippeer) {
            $sippeer->delete();  // Удаляем запись
            return response()->json(['message' => 'Sippeer успешно удалёно.']);
        }

        return response()->json(['message' => 'Sippeer не найдено.'], 404);
    }
}
