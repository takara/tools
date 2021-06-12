<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

	protected function extract(array $data, string $path)
    {
        if (empty($path)) {
            return $data;
        }
        $paths = explode('.',$path);
        $ret = [];
        foreach ($paths as $cidx => $cur) {
            //print  "$cur\n";
            if (preg_match("/\(([A-Za-z\|]+)\)/", $cur, $match)) {
                $fields = explode("|", $match[1]);
                foreach ($fields as $field) {
                    $ret[] = $data[$field];
                }
                continue;
            }
            if (preg_match("/^[A-Za-z0-9]+\$/", $cur, $match)) {
                $idx = array_shift($match);
                if (array_key_exists($idx, $data) === false) {
                    throw new \Exception("指定のパス($path)がありません");
                }
                if (is_array($data[$idx])) {
                    $data = $data[$idx];
                } else {
                    $ret = $data[$idx];
                }
                continue;
            }
            if ($cur == '{n}') {
                $npath = implode('.',array_slice($paths, $cidx+1));
                foreach ($data as $row) {
                    $ret[] = $this->extract($row, $npath);
                }
                break;
            }

            throw new \Exception("未対応(path={$path})");
        }
        return $ret;
    }
}
