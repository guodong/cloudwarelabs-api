<?php

namespace App\Http\Controllers;

use App\Models\Cloudware;
use App\Models\Homework;
use App\Models\Instance;
use App\Models\Setting;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7;
use Illuminate\Validation\Rules\In;

class HomeworkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $homeworks = Homework::all();
        foreach ($homeworks as $homework) {
            $homework->teacher;
        }
        return $homeworks;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $homework = Homework::create([
            'title' => $request->title,
            'description' => $request->description,
            'teacher_id' => $request->user()->id,
        ]);
        return $homework;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Homework::find($id);
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

    public function submissions(Request $request, $homework_id)
    {
        return Homework::find($homework_id)->submissions;
    }
}
