<?php

namespace Tests\Models;

use App\Models\BookTools;
use Tests\TestCase;

class BookToolsTest extends TestCase
{
    public function test_ファイル名変更()
    {
        $res = Booktools::converOutputZipFilename("[hoge] fuga.zip");
        $this->assertEquals("[hoge]fuga.zip", $res);
    }
}
