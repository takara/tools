<?php

namespace App\Console\Commands\switchbot;

use App\Models\SwitchBotAPI;
use Illuminate\Console\Command;

class aircon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sb:aircon {power=off : 電源}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'エアコン操作';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$power = $this->argument("power");
		$onf = \Cache::get("aircon.power");
		if ($power == $onf) {
			\Log::info("同じ状態($onf)なので何もしない");
			return 0;
		}
		$id = app("config")->get("app.switchbot.aircon_id");
		//print "$id\n";
		$json = json_encode([
			"command" => "setAll",
			"parameter" => "26,2,1,$power",
			"commandType" => "command",
		]);
		$res = SwitchBotAPI::getInstance()->commands($id, $json);
		\Cache::put("aircon.power", $power);
		print_r($res);
		\Log::info("エアコン状態($power)変更");
        return 0;
    }
}
