<?php

namespace App\Http\Controllers;

use App\Models\Cloudware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

class CloudwareController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Cloudware::all();
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
        $cloudware = Cloudware::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->image,
            'logo' => $request->logo
        ]);
        return $cloudware;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Cloudware::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cloudware = Cloudware::find($id);
        $cloudware->name = $request->name;
        $cloudware->description = $request->description;
        $cloudware->image = $request->image;
        $cloudware->logo = $request->logo;
        $cloudware->save();
        return $cloudware;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cloudware = Cloudware::find($id);
        $cloudware->delete();
        return ['delete success'];
    }
}
