<?php

namespace App\Http\Controllers;

use App\Models\Cloudware;
use App\Models\Homework;
use App\Models\Instance;
use App\Models\Setting;
use App\Models\Submission;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7;
use Illuminate\Validation\Rules\In;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $submissions = $request->user()->submissions;
        return $submissions;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $submission = Submission::create([
            'homework_id' => $request->homework_id,
            'description' => $request->description,
            'instance_id' => $request->instance_id,
            'user_id' => $request->user()->id,
        ]);
        return $submission;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Submission::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return ['result' => 'success'];
    }

}
