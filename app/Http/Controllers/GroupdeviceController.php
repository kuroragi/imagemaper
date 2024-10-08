<?php

namespace App\Http\Controllers;

use App\CPU\Helpers;
use App\Models\Groupdevice;
use App\Http\Controllers\Controller;
use App\Models\Asset;
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
        // $group = Groupdevice::all();
        $group = Helpers::getGroup()['data'];
        // dd($group);
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
            
            $file->storeAs('/img/gdevice/', $request->name.'.'.$fileext, ['disk' => 'myfile']);

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
        $areas = Imagemap::where('id_group', $groupdevice->id)->get();
        $nodes = collect();
        foreach ($areas as $m) {
            $mapnodes = explode(',', $m->coordinate);
            $nodes = collect($mapnodes)->chunk(2)->map(function($pair, $index) use ($m){
                return [
                    'x' => $pair->first(),
                    'y' => $pair->last(),
                    'nodeName' => 'savedNode_'.$m->id.'_'.($index + 1),
                ];
            })->all();
        }
        $asset = Asset::groupBy('name')->get();
        // dd($areas);
        return view('groupdevice.areas', [
            'groupdevice' => $groupdevice,
            'areas' => $areas,
            'nodes' => $nodes,
            'asset' => $asset,
        ]);
    }

    public function showGroup(int $id){
        $group = Helpers::getGroupSelect($id)['data'][0];
        $map = Helpers::getImageMap($id)['data'];
        $nodes = collect();
        foreach ($map as $m) {
            $mapnodes = explode(',', $m->coordinate);
            // dd($m->coordinate);
            collect($mapnodes)->chunk(2)->map(function($pair, $index) use ($m, $nodes){
                $nodes->push([
                    'x' => $pair->first(),
                    'y' => $pair->last(),
                    'nodeName' => 'savedNode_'.$m->id.'_'.($index + 1),
                    'nodeIndex' => $index,
                ]);
            })->all();
        }
        // // $nodes = explode(',', $map);
        // dd($nodes);
        // dd($map);
        return view('groupdevice.allarea', [
            'groupdevice' => $group,
            'areas' => $map,
            'nodes' => $nodes,
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
