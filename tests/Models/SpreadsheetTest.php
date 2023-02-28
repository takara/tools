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
	/*
    public function test_テスト()
    {
        $this->setTestDateTime("2022/02/20 16:00:00");
        $param = Spreadsheet::getInstance()
            ->getCreateTodayParam();
        $this->assertEquals([
            'fromSpreadSheetId' => '1odNii9MzEzuJvp9sLZ1_1oAQu1xGmkVKu9txFo7fwfc',
            'fromSheetId'       => '808904582',
            'sheetTitle'        => '02/20',
            'toSpreadSheetId'   => '1-BM95x5rJqpFJ4JG1I4LPfw0gKMGW4XUjXW58H_0qCI',
        ], $param);

        $this->setTestDateTime("2022/02/14 16:00:00");
        $param = Spreadsheet::getInstance()
            ->getCreateTodayParam();
        $this->assertEquals([
            'fromSpreadSheetId' => '1odNii9MzEzuJvp9sLZ1_1oAQu1xGmkVKu9txFo7fwfc',
            'fromSheetId'       => '1902657632',
            'sheetTitle'        => '02/14',
            'toSpreadSheetId'   => '1-BM95x5rJqpFJ4JG1I4LPfw0gKMGW4XUjXW58H_0qCI',
        ], $param);

        $this->setTestDateTime("2022/02/19 16:00:00");
        $param = Spreadsheet::getInstance()
            ->getCreateTodayParam();
        $this->assertEquals([
            'fromSpreadSheetId' => '1odNii9MzEzuJvp9sLZ1_1oAQu1xGmkVKu9txFo7fwfc',
            'fromSheetId'       => '724721287',
            'sheetTitle'        => '02/19',
            'toSpreadSheetId'   => '1-BM95x5rJqpFJ4JG1I4LPfw0gKMGW4XUjXW58H_0qCI',
        ], $param);
    }
	 */
}
