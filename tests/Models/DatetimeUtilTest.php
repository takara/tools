<?php

namespace Tests\Models;

use App\Models\DatetimeUtil;
use Exception;
use PHPUnit\Framework\TestCase;

class DatetimeUtilTest extends TestCase
{
    /**
     * @return void
     * @noinspection NonAsciiCharacters
     * @throws Exception
     */
    public function test_現在時刻()
    {
        $res = DatetimeUtil::now();
        $this->assertNotFalse(
            preg_match('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}]$/', $res->format("Y/m/d H:i:s"))
        );
    }

    /**
     * @noinspection NonAsciiCharacters
     * @throws Exception
     */
    public function test_時間変更()
    {
        DatetimeUtil::setDatetime("2020/01/01 01:02:03");
        $res = DatetimeUtil::now();
        $this->assertEquals(
            "2020/01/01 01:02:03",
            $res->format("Y/m/d H:i:s")
        );
    }
}
