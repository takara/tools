<?php

namespace App\Console\Commands\switchbot;

use App\Models\SwitchBotAPI;
use Illuminate\Console\Command;

class thermometer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sb:thermometer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '温度取得';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$id = app("config")->get("app.switchbot.thermometer_id");
		$res = SwitchBotAPI::getInstance()->getStatus($id);
		$temp = $res['body']['temperature'];
		\Log::info("温度:[$temp]");
		$temp = (int)($temp * 10);
		print $temp;
        return 0;
    }
}
