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
    public function test_テスト()
    {
        /*
        Spreadsheet::getInstance()
            ->createTodayActivity("1odNii9MzEzuJvp9sLZ1_1oAQu1xGmkVKu9txFo7fwfc",
//                "1902657632", // 02/14
            "808904582", // 02/13
                "02/15", "1Dx2PywX3utjOWxotkDye2khqzPfk7JGB_4zqWr2P3Qg");
        */
        $this->setTestDateTime("2022/02/20 16:00:00");
        $param = Spreadsheet::getInstance()
            ->getCreateTodayParam();
        $this->assertEquals([
            'fromSpreadSheetId' => '1odNii9MzEzuJvp9sLZ1_1oAQu1xGmkVKu9txFo7fwfc',
            'fromSheetId'       => '808904582',
            'sheetTitle'        => '02/20',
            'toSpreadSheetId'   => '1Dx2PywX3utjOWxotkDye2khqzPfk7JGB_4zqWr2P3Qg',
        ], $param);

        $this->setTestDateTime("2022/02/14 16:00:00");
        $param = Spreadsheet::getInstance()
            ->getCreateTodayParam();
        $this->assertEquals([
            'fromSpreadSheetId' => '1odNii9MzEzuJvp9sLZ1_1oAQu1xGmkVKu9txFo7fwfc',
            'fromSheetId'       => '1902657632',
            'sheetTitle'        => '02/14',
            'toSpreadSheetId'   => '1Dx2PywX3utjOWxotkDye2khqzPfk7JGB_4zqWr2P3Qg',
        ], $param);

        $this->setTestDateTime("2022/02/19 16:00:00");
        $param = Spreadsheet::getInstance()
            ->getCreateTodayParam();
        $this->assertEquals([
            'fromSpreadSheetId' => '1odNii9MzEzuJvp9sLZ1_1oAQu1xGmkVKu9txFo7fwfc',
            'fromSheetId'       => '724721287',
            'sheetTitle'        => '02/19',
            'toSpreadSheetId'   => '1Dx2PywX3utjOWxotkDye2khqzPfk7JGB_4zqWr2P3Qg',
        ], $param);
    }
}
