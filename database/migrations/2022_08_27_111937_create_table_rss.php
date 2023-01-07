<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRss extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_rss', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        \DB::statement(
			"CREATE TABLE `rss` (\n".
			"  `url` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL',\n".
			"  `title` varchar(256) NOT NULL COMMENT 'タイトル',\n".
			"  `notice` tinyint(1) NOT NULL COMMENT '通知',\n".
			"  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n".
			"  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n".
			"  PRIMARY KEY (`url`)\n".
			") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci\n".
            ""
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rss');
    }
}
