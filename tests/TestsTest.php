<?php

namespace Tests\Models;

use Cake\Utility\Hash;
use Tests\TestCase;

class TestsTest extends TestCase
{
    public function test_配列操作()
    {
		$data = [
			[
				"a" => 1,
				"b" => 2,
				"c" => 3,
			], [
				"a" => 4,
				"b" => 5,
				"c" => 6,
			]
		];
        $res = $this->extract($data, '{n}.(a|c)');
        $this->assertEquals([[1, 3], [4, 6]], $res);
    }
}
