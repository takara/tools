<?php

namespace App\Console\Commands\Sheet;

use App\Models\Spreadsheet;
use Illuminate\Console\Command;

class CreateTodayActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:sheet:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '今日の分のアクティビティシートを作成する';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Spreadsheet::getInstance()
            ->createTodayActivity();
        return 0;
    }
}
