<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExtensionResource;
use App\Extension;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $extensions = Extension::orderBy('priority', 'asc')
            ->orderBy('id', direction: 'desc')
            ->paginate(50);
        return ExtensionResource::collection($extensions);
    }


    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        // Валидация данных
        $validatedData = $request->validate([
            'context' => 'required|string|max:255',
            'exten' => 'required|string|max:255',
            'priority' => 'required|integer',
            'app' => 'required|string|max:255',
            'appdata' => 'nullable|string|max:255',
        ]);

        $extension = Extension::create($validatedData);

        return new ExtensionResource($extension);
    }

    /**
     * Display the specified resource.
     *
     */
    public function show(Extension $Extension)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit($id)
    {
        $extension = Extension::findOrFail($id);
        return new ExtensionResource($extension);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'context' => 'required|string|max:255',
            'exten' => 'required|string|max:255',
            'priority' => 'required|integer',
            'app' => 'required|string|max:255',
            'appdata' => 'nullable|string|max:255',
        ]);

        $extension = Extension::findOrFail($id);
        $extension->update($validatedData);
        return new ExtensionResource($extension);
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy($id)
    {
        $extension = Extension::find($id);

        $extension = Extension::findOrFail($id);
         if ($extension) {
            $extension->delete();  // Удаляем запись
            return response()->json(['message' => 'Расширение успешно удалёно.']);
        }

        return response()->json(['message' => 'Расширение не найдено.'], 404);
    }

    public function getContexts()
    {
        $contexts = Extension::all()->pluck('context')->unique(); ;
        $data = $contexts->map(function ($context) {
            return [
                'text' => ucfirst($context),
                'value' => $context
            ];
        });
    
        return response()->json([
            'data' => $data
        ]);
    }
}
