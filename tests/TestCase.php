<?php

namespace Tests;

use App\Console\Kernel;
use App\Models\DatetimeUtil;
use Exception;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
    }

    /**
     * @param $data
     * @param string $path
     * @param bool $needField
     * @return array|mixed
     * @throws Exception
     */
    protected function extract($data, string $path, bool $needField = true)
    {
        if (empty($path)) {
            return $data;
        }
        $paths = explode('.',$path);
        $ret = [];
        foreach ($paths as $curIdx =>$cur) {
            // フィールド複数指定
            if (preg_match("/\(([A-Za-z0-9_|]+)\)/", $cur, $match)) {
                $fields = explode("|", $match[1]);
                foreach ($fields as $field) {
                    if ($needField) {
                        if (is_object($data)) {
                            $ret[$field] = $data->$field ?? "undefined property $field";
                        } else {
                            $ret[$field] = $data[$field] ?? "undefined $field";
                        }
                    } else {
                        if (is_object($data)) {
                            $ret[] = $data->$field ?? "undefined property $field";
                        } else {
                            $ret[] = $data[$field] ?? "undefined $field";
                        }
                    }
                }
                continue;
            }
            // 添え字指定
            if (preg_match("/^[A-Za-z0-9_]+\$/", $cur, $match)) {
                $idx = array_shift($match);
                if (is_array($data) && array_key_exists($idx, $data) === false) {
                    throw new Exception("指定のパス($path)がありません");
                }
                switch (true) {
                    case is_array($data):
                    case (is_object($data) && isset($data[$idx])): // オブジェクトでも配列のアクセスできるやつ
                        if (is_array($data[$idx]) || is_object($data[$idx])) {
                            $data = $data[$idx];
                        } else {
                            if ($needField) {
                                $ret[$idx] = $data[$idx];
                            } else {
                                $ret = $data[$idx];
                            }
                        }
                        break;
                    case is_object($data):
                        if (is_array($data->$idx) || is_object($data->$idx)) {
                            $data = $data[$idx];
                        } else {
                            if ($needField) {
                                $ret[$idx] = $data->$idx;
                            } else {
                                $ret = $data[$idx];
                            }
                        }
                        break;
                }
                continue;
            }
            // n兼レコード指定
            if ($cur == '{n}') {
                $nPath = implode('.',array_slice($paths, $curIdx+1));
                foreach ($data as $row) {
                    $ret[] = $this->extract($row, $nPath, $needField);
                }
                break;
            }
            if ($cur == "*") {
                if (is_object($data)) {
                    foreach (get_object_vars($data) as $idx => $value) {
                        if ($needField) {
                            $ret[$idx] = $data->$idx;
                        } else {
                            $ret = $data[$idx];
                        }
                    }
                } else if (is_array($data)) {
                    foreach ($data as $idx => $value) {
                        if ($needField) {
                            $ret[$idx] = $value;
                        } else {
                            $ret = $value;
                        }
                    }
                }
                break;
            }

            throw new Exception("未対応(path=$path)");
        }
        return $ret;
    }

    protected function setTestDateTime(string $datetime)
    {
        DatetimeUtil::setDatetime($datetime);
    }
}
