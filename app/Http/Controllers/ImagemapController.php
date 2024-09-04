<?php

namespace App\Http\Controllers;

use App\CPU\Helpers;
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
            $data = ['id_group' => $request->id_group, 'name' => $area['alt'], 'coordinate' => $area['coords'], 'shape' => $area['shape'], 'status' => $area['status'], 'description' => $area['description'], 'id_asset' => $area['id_asset']];

            Imagemap::create($data);
        }

        return response()->json(['areas' => $request->areas]);
    }
    
    public function storeMap(Request $request)
    {
        // return response()->json($request);
        foreach ($request->areas as $area) {
            $data = ['id_group' => $request->id_group, 'name' => $area['alt'], 'coordinate' => $area['coords'], 'shape' => $area['shape'], 'status' => $area['status'], 'description' => $area['description'], 'id_asset' => $area['id_asset']];

            // return response()->json(['msg' => $data]);

            $status = Helpers::postMap($data);

            return response()->json(['msg' => $status]);
        }

        // return response()->json(['areas' => $request->areas]);
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
    
    public function updateMap(Request $request)
    {
        // return response()->json($request);
        $data = ['id' => $request->id, 'id_asset_group' => $request->areas['id_asset_group'], 'name' => $request->areas['alt'], 'coordinate' => $request->areas['coords'], 'shape' => $request->areas['shape'], 'status' => $request->areas['status'], 'description' => $request->areas['description'], 'id_asset' => $request->areas['id_asset'], 'device_type' => $request->areas['device_type'], 'meta' => $request->areas['meta']];

        // return response()->json(['msg' => $data]);

        $status = Helpers::updateMap($data);

        return response()->json(['msg' => $status]);

        // return response()->json(['areas' => $request->areas]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Imagemap  $imagemap
     * @return \Illuminate\Http\Response
     */
    public function destroy(Imagemap $imagemap)
    {
        // return response()->json("berhasil masuk hapus");

        Imagemap::destroy('id', $imagemap->id);

        return response()->json(['msg' => 'Berhasil Hapus data '.$imagemap->name, 'id' => $imagemap->id]);
    }

    public function destroyMap(Imagemap $imagemap)
    {
        // return response()->json("berhasil masuk hapus");

        Helpers::deleteMap($imagemap->id);

        return response()->json(['msg' => 'Berhasil Hapus data '.$imagemap->name, 'id' => $imagemap->id]);
    }
}
