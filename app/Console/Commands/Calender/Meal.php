<?php

namespace App\Console\Commands\Calender;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Config\Repository;

class Meal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:calendar:meal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '日記カレンダーに食事のスケジュールを書き込む';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle(): int
    {
        /**
         * @var $config Repository
         */
        $config           = app('config');
        $calendarId = $config->get('app.calendar.calendar_id');
        //$calendarId = "cdhonr4ghfmulivfcruk8bmbu0@group.calendar.google.com"; // 日記
        //$calendarId = "1h56the5v2divt7f0ob1kbf92k@group.calendar.google.com"; // 湘南

        \App\Models\Meal::getInstance()
            ->execute($calendarId);

        return 0;
    }
}
