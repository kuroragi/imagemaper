<?php

namespace App\Http\Controllers;

use App\Models\Imagemap;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImagemapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        // return response()->json($request);
        foreach ($request->areas as $area) {
            $data = ['group_id' => $request->group_id, 'name' => $area['alt'], 'coordinate' => $area['coords'], 'shape' => $area['shape'], 'status' => $area['status'], 'description' => $area['description']];

            Imagemap::create($data);
        }

        return response()->json(['areas' => $request->areas]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Imagemap  $imagemap
     * @return \Illuminate\Http\Response
     */
    public function show(Imagemap $imagemap)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Imagemap  $imagemap
     * @return \Illuminate\Http\Response
     */
    public function edit(Imagemap $imagemap)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Imagemap  $imagemap
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Imagemap $imagemap)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Imagemap  $imagemap
     * @return \Illuminate\Http\Response
     */
    public function destroy(Imagemap $imagemap)
    {
        //
    }
}
