<?php

namespace Tests\Models;

use App\Models\BookTools;
use Tests\TestCase;

class BookToolsTest extends TestCase
{
    /**
     */
    public function test__必要があればファイル名変更()
	{
		$filename ="/FC2 PPV 2240112.mp4";
        $res = Booktools::renameFormat($filename, false);
		$this->assertEquals("/FC2-PPV-2240112.mp4", $res);
		$filename ="/aaa/bbb/FC2 PPV 2240112.jpg";
        $res = Booktools::renameFormat($filename, false);
		$this->assertEquals("/aaa/bbb/FC2-PPV-2240112.jpg", $res);
		$filename ="Herennia---Touching-Herself_Stunning-1080p_top-modelz.org.mp4";
        $res = Booktools::renameFormat($filename, false);
		$this->assertEquals("Herennia---Touching-Herself_Stunning-1080p_top-modelz.org.mp4", $res);
		$filename ="FC2PPV-1268153-1.mp4";
        $res = Booktools::renameFormat($filename, false);
		$this->assertEquals("./FC2-PPV-1268153-1.mp4", $res);
	}

    /**
     * @return void
     * @noinspection NonAsciiCharacters
     */
	/*
    public function test__ファイル名変更()
    {
        $res = Booktools::converOutputZipFilename("[hoge] fuga.zip");
        $this->assertEquals("[hoge]fuga.zip", $res);
    }
	 */

    /**
     * @return void
     * @noinspection NonAsciiCharacters
     */
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

    /**
     * @return void
     * @noinspection NonAsciiCharacters
     */
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
