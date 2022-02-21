<?php

namespace App\Models;

use DateTime;
use DateTimeZone;
use Exception;

class DatetimeUtil extends DateTime
{
    /**
     * @var string|null
     */
    protected static $now = null;

    /**
     * @throws Exception
     */
    public static function now(): DatetimeUtil
    {
        $timezone = new DateTimeZone('Asia/Tokyo');
        if (is_null(static::$now)) {
            $ret = new self('now', $timezone);
        } else {
            $ret = new self(static::$now, $timezone);
        }
        return $ret;
    }


    public static function setDatetime(string $datetime)
    {
        static::$now = $datetime;
    }
}
