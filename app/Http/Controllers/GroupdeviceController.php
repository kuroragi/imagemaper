<?php

namespace App\Http\Controllers;

use App\Models\Groupdevice;
use App\Http\Controllers\Controller;
use App\Models\Imagemap;
use Illuminate\Http\Request;

class GroupdeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $group = Groupdevice::all();
        return view('groupdevice.group', [
            'group' => $group,
        ]);
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
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        $validatedData += [
            'type' => 'single',
            'coordinate' => '',
            'collection_id' => '',
        ];

        if($request->image != ''){
            // dd($request->file('file'));
            $typefile = explode('/', $request->file('image')->getMimeType());

            $fileext = $request->file('image')->extension();

            $file = $request->file('image');
            
            $file->storeAs('/img/groupdevice/', $request->name.'.'.$fileext, ['disk' => 'myfile']);

            $validatedData['image'] = $request->name.'.'.$fileext;
        }

        Groupdevice::create($validatedData);

        return redirect('/groupdevice');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Groupdevice  $groupdevice
     * @return \Illuminate\Http\Response
     */
    public function show(Groupdevice $groupdevice)
    {
        $areas = Imagemap::where('group_id', $groupdevice->id)->get();
        // dd($areas);
        return view('groupdevice.areas', [
            'groupdevice' => $groupdevice,
            'areas' => $areas,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Groupdevice  $groupdevice
     * @return \Illuminate\Http\Response
     */
    public function edit(Groupdevice $groupdevice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Groupdevice  $groupdevice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Groupdevice $groupdevice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Groupdevice  $groupdevice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Groupdevice $groupdevice)
    {
        //
    }
}
