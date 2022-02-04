<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLoto6 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::statement(
			"CREATE TABLE `rbl_loto6` (".
			  "`id` int NOT NULL COMMENT '開催回',".
			  "`date` int NOT NULL COMMENT '日付',".
			  "`num1` int NOT NULL COMMENT '第1数字',".
			  "`num2` int NOT NULL COMMENT '第2数字',".
			  "`num3` int NOT NULL COMMENT '第3数字',".
			  "`num4` int NOT NULL COMMENT '第4数字',".
			  "`num5` int NOT NULL COMMENT '第5数字',".
			  "`num6` int NOT NULL COMMENT '第6数字',".
			  "`numb` int NOT NULL COMMENT 'BONUS数字',".
			  "`hitnum1` int NOT NULL COMMENT '1等口数',".
			  "`hitnum2` int NOT NULL COMMENT '2等口数',".
			  "`hitnum3` int NOT NULL COMMENT '3等口数',".
			  "`hitnum4` int NOT NULL COMMENT '4等口数',".
			  "`hitnum5` int NOT NULL COMMENT '5等口数',".
			  "`reward1` int NOT NULL COMMENT '1等賞金',".
			  "`reward2` int NOT NULL COMMENT '2等賞金',".
			  "`reward3` int NOT NULL COMMENT '3等賞金',".
			  "`reward4` int NOT NULL COMMENT '4等賞金',".
			  "`reward5` int NOT NULL COMMENT '5等賞金',".
			  "`carryover` int NOT NULL COMMENT 'キャリーオーバー',".
			  "PRIMARY KEY (`id`)".
			") ENGINE=InnoDB DEFAULT CHARSET=utf8".
			"");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loto6');
    }
}
