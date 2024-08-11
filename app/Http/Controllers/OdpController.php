<?php

namespace App\Http\Controllers;

use App\Models\odp;
use App\Http\Controllers\Controller;
use App\Models\odpdetail;
use Illuminate\Http\Request;

class OdpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $odp = odp::all();
        return view('odp.odp', [
            'odp' => $odp,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required',
            'file' => 'required',
        ]);

        if($request->file != ''){
            // dd($request->file('file'));
            $typefile = explode('/', $request->file('file')->getMimeType());

            $fileext = $request->file('file')->extension();

                $file = $request->file('file');
                
                $file->storeAs('/img/', $request->nama.'.'.$fileext, ['disk' => 'myfile']);

                $validatedData['gambar'] = $request->nama.'.'.$fileext;
        }

        odp::create($validatedData);

        return redirect('/odp');
    }

    /**
     * Display the specified resource.
     */
    public function show(odp $odp)
    {
        $detail = odpdetail::where('odp_id', $odp->id)->get();
        // dd($detail);
        return view('odp.detail', [
            'odp' => $odp,
            'areas' => $detail,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(odp $odp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, odp $odp)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(odp $odp)
    {
        //
    }
}
