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
		$humidity = $res['body']['humidity'];
		\Log::debug("温度:[$temp]");
		\Log::debug("湿度:[$humidity]");
		$di = 0.81 * $temp +  0.01 * $humidity * ( 0.99 * $temp - 14.3) + 46.3;
		$str = $this->getDIString($di);
		\Log::debug("不快指数:[$di][$str]");
		//\Log::debug(print_r($res, true));
		$temp = (int)($temp * 10);
		print $temp;
        return 0;
    }

	protected function getDIString($di) : string
	{
		$list = [
			[ 0,55,"寒い"],
			[55,60,"肌寒い"],
			[60,65,"何も感じない"],
			[65,70,"快い"],
			[70,75,"暑くない"],
			[75,80,"やや暑い"],
			[80,85,"暑くて汗が出る"],
			[85,99,"暑くてたまらない"],
		];
		$ret = "";
		foreach ($list as $cond) {
			list($s, $e, $str) = $cond;
			if ($s<= $di && $di < $e) {
				$ret = $str;
				break;
			}
		}
		return $ret;
	}
}
