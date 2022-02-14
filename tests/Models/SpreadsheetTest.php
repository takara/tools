<?php

namespace Tests\Models;

use App\Models\Spreadsheet;
use Tests\TestCase;

class SpreadsheetTest extends TestCase
{
    /**
     * @return void
     * @noinspection NonAsciiCharacters
     */
    public function test_クラス作成()
    {
        $sheet = Spreadsheet::getInstance();
        $this->assertEquals("App\Models\Spreadsheet", get_class($sheet));
    }

    /**
     * @noinspection NonAsciiCharacters
     */
/*
    public function test_テスト()
    {
        $res =Spreadsheet::getInstance()
            ->getSheet();
        $this->assertCount(36, $res);
    }
*/
}
