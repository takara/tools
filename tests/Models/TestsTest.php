<?php

namespace Tests\Models;

use Tests\TestCase;

class TestsTest extends TestCase
{
    /**
     * @return void
     * @noinspection NonAsciiCharacters
     */
    public function test__配列操作()
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
        $this->assertEquals([
            [
                'a' => 1,
                'c' => 3,
            ], [
                'a' => 4,
                'c' => 6
            ]
        ], $res);
    }
}
