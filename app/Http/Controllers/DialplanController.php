<?php

namespace App\Http\Controllers;

use App\Http\Resources\DialplanResource;
use App\Dialplan;
use Illuminate\Http\Request;

class DialplanController extends Controller
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
        $dialplans = Dialplan::orderBy('id', 'desc')
            ->paginate(50);
        return DialplanResource::collection($dialplans);
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
            'destination' => 'required|string|max:255',
            'context' => 'required|string|max:255',
            'priority' => 'required|integer',
            'action' => 'required|string|max:255',
            'params' => 'nullable|string|max:255',
        ]);

        $dialplan = Dialplan::create($validatedData);

        return new DialplanResource($dialplan);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $dialplan = Dialplan::findOrFail($id);
        return new DialplanResource($dialplan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dialplan = Dialplan::findOrFail($id);

        $validatedData = $request->validate([
            'destination' => 'required|string|max:255',
            'context' => 'required|string|max:255',
            'priority' => 'required|integer',
            'action' => 'required|string|max:255',
            'params' => 'nullable|string|max:255',
        ]);

        $dialplan->update([
            'destination' => $validatedData['destination'],
            'context' => $validatedData['context'],
            'priority' => $validatedData['priority'],
            'action' => $validatedData['action'],
            'params' => $validatedData['params'] ?? null,
        ]);

        return new DialplanResource($dialplan);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dialplan = Dialplan::find($id);

        $dialplan = Dialplan::findOrFail($id);
         if ($dialplan) {
            $dialplan->delete();  // Удаляем запись
            return response()->json(['message' => 'Диалплан успешно удалён.']);
        }

        return response()->json(['message' => 'Диалплан не найден.'], 404);
    }
}
