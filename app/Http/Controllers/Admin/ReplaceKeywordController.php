<?php

namespace App\Http\Controllers\admin;

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
     * @param  \App\Models\ReplaceKeyword  $replaceKeword
     * @return \Illuminate\Http\Response
     */
    public function edit(ReplaceKeyword $replaceKeword)
    {
        $list = ReplaceKeyword::all();
        return view('admin/replace_keyword/index', ['list' => $list]);
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
}
