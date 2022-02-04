<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReplaceKeyword extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('replace_keyword')->insert([
            [
                'pattern'=> '\] ',
                'keyword'=> ']',
                'created_at'=>now(), 'updated_at'=>now(),
            ],
            [
                'pattern'=> '\]_',
                'keyword'=> ']',
                'created_at'=>now(), 'updated_at'=>now(),
            ],
            [
                'pattern'=> '\[成年コミック\]',
                'keyword'=> null,
                'created_at'=>now(), 'updated_at'=>now(),
            ],
            [
                'pattern'=> '\(成年コミック\)[ ]?',
                'keyword'=> null,
                'created_at'=>now(), 'updated_at'=>now(),
            ],
            [
                'pattern'=> '\(一般コミック\)[ ]?',
                'keyword'=> null,
                'created_at'=>now(), 'updated_at'=>now(),
            ],
            [
                'pattern'=> '\(同人誌\)[ ]?',
                'keyword'=> null,
                'created_at'=>now(), 'updated_at'=>now(),
            ],
            [
                'pattern'=> '\(Adult Manga\)[ ]?',
                'keyword'=> null,
                'created_at'=>now(), 'updated_at'=>now(),
            ],
            [
                'pattern'=> '\(C[0-9]+\)[ ]?',
                'keyword'=> null,
                'created_at'=>now(), 'updated_at'=>now(),
            ],
        ]);
    }
}
