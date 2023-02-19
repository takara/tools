<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReplaceKeyword;
use Illuminate\Http\Request;

class ReplaceKeywordController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $e = new \Exception();
        //print $e->getTraceAsString();exit;
        $list = ReplaceKeyword::all();
        return view('admin/replace_keyword/index', ['list' => $list]);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ReplaceKeyword  $replaceKeword
     * @return \Illuminate\Http\Response
     */
    public function show(ReplaceKeyword $replaceKeword)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        // O http://tools/replace_keyword/edit/1
        // X http://tools/reprace_keyword/edit/1
        //print_r($replaceKeword);exit;
        //$list = ReplaceKeyword::all();
        //return view('admin/replace_keyword/index', ['list' => $list]);
        /**
         * @var \App\Models\ReplaceKeyword  $replaceKeword
         */
        $replaceKeword = ReplaceKeyword::find($id);
        return $replaceKeword->pattern;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ReplaceKeyword  $replaceKeword
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReplaceKeyword $replaceKeword)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReplaceKeyword  $replaceKeword
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReplaceKeyword $replaceKeword)
    {
        //
    }
    public function test()
    {
        return view('admin/replace_keyword/index',['list'=>[]]);
    }
    public function member($member_id)
    {
        print_r($member_id);
    }
}
