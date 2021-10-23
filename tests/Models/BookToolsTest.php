<?php

namespace Tests\Models;

use App\Models\BookTools;
use Tests\TestCase;

class BookToolsTest extends TestCase
{
    public function test__ファイル名変更()
    {
        $res = Booktools::converOutputZipFilename("[hoge] fuga.zip");
        $this->assertEquals("[hoge]fuga.zip", $res);
    }

    public function test__ファイル名ソート()
	{
		$files = [
			'c',
			'b',
			'a',
		];
        $res = Booktools::sortPages($files);
		$this->assertEquals([
			'c',
			'b',
			'a',
		], $res);
		
		$files = [
			'3',
			'2',
			'1',
		];
        $res = Booktools::sortPages($files);
		$this->assertEquals([
			'1',
			'2',
			'3',
		], $res);
	}

    public function test__ファイル名ソート_週間アスキー()
	{
		$files = [
			'週刊アスキー Weekly ASCII 2021-10-19_imgs-0150.jpg',
			'週刊アスキー Weekly ASCII 2021-10-19_imgs-0100.jpg',
			'週刊アスキー Weekly ASCII 2021-10-19_imgs-0001.jpg',
		];
        $res = Booktools::sortPages($files);
		$this->assertEquals([
			'週刊アスキー Weekly ASCII 2021-10-19_imgs-0001.jpg',
			'週刊アスキー Weekly ASCII 2021-10-19_imgs-0100.jpg',
			'週刊アスキー Weekly ASCII 2021-10-19_imgs-0150.jpg',
		], $res);
	}
}
