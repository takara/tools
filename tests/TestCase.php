<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param $data
     * @param string $path
     * @param bool $needField
     * @return array|mixed
     */
    protected function extract($data, string $path, bool $needField = true)
    {
        if (empty($path)) {
            return $data;
        }
        $paths = explode('.',$path);
        $ret = [];
        foreach ($paths as $cidx => $cur) {
            // フィールド複数指定
            if (preg_match("/\(([A-Za-z0-9_\|]+)\)/", $cur, $match)) {
                $fields = explode("|", $match[1]);
                foreach ($fields as $field) {
                    if ($needField) {
                        if (is_object($data)) {
                            $ret[$field] = $data->$field ?? "undefined propaty $field";
                        } else {
                            $ret[$field] = $data[$field] ?? "undefined $field";
                        }
                    } else {
                        if (is_object($data)) {
                            $ret[] = $data->$field ?? "undefined propaty $field";
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
                $npath = implode('.',array_slice($paths, $cidx+1));
                foreach ($data as $row) {
                    $ret[] = $this->extract($row, $npath, $needField);
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
                            $ret[$idx] = $data[$idx];
                        } else {
                            $ret = $data[$idx];
                        }
                    }
                }
                break;
            }

            throw new Exception("未対応(path=$path)");
        }
        return $ret;
    }
}
