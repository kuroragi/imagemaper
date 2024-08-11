<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\odpdetail;
use Illuminate\Http\Request;

class OdpdetailController extends Controller
{
    public function store(Request $request)
    {
        // return response()->json($request);
        foreach ($request->areas as $area) {
            $data = ['odp_id' => $request->odp_id, 'alt' => $area['alt'], 'coords' => $area['coords'], 'shape' => $area['shape'], 'status' => $area['status'], 'deskripsi' => $area['deskripsi']];

            odpdetail::create($data);
        }

        return response()->json(['areas' => $request->areas]);
    }
}
