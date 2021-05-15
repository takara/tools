<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplaceKeword extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement(
            "CREATE TABLE `replace_keword` (\n".
            "  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',\n".
            "  `pattern` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '変更対象',\n".
            "  `keywoed` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '変更後文字列',\n".
            "  `created_at` timestamp NULL DEFAULT NULL COMMENT '作成日時',\n".
            "  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新日時',\n".
            "  PRIMARY KEY (`id`)\n".
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='キーワード変換'\n".
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
        Schema::dropIfExists('replace_keword');
    }
}
