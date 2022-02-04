<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;


class BookController extends Controller
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        return view('book/view', []);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        //
    }

    public function showPage(int $id)
    {
        $zip = new \ZipArchive();
        $res = $zip->open("/Users/takara/Downloads/[向正義]淫落遊戯.zip");
        $res = $zip->open('/Volumes/Data/TSF/[小武]/[小武] 快感チェンジ♂⇔♀～初めては女のカラダで～.zip');
        if ($res !== true) {
            throw new \Exception("ファイルを開けません（{$res}）");
        }
        // X 2b b9
        // O 15 52
        $max = $zip->count();
        if ($id >= $max) {
            $id = $max - 1;
        }
        $name = $zip->getNameIndex($id);
        $stat = $zip->statName($name);
        $size = $stat['size'];
        //$size = 7769273;
        $fp = $zip->getStream($name);
        //$jpeg = fread($fp, $size);
        $jpeg = '';
        while (!feof($fp)) {
            $jpeg .= fread($fp, 4096);
        }
        //$zip->extractTo("/Users/takara/",[$name]);
        $zip->close();
        $readsize = strlen($jpeg);
//        file_put_contents("/Users/takara/a.jpg", $jpeg);
//        $jpeg = file_get_contents("/Users/takara/002.jpg");
        //return ":$id:".json_encode($res).":".$size.":".$readsize;
        //return response($jpeg)
        //    ->header('Content-Type', 'image/jpg');
        return \response()->stream(function() use($jpeg) {
            echo $jpeg;
        }, 200, ["Content-Type"=> "image/jpg"]);
    }
}
