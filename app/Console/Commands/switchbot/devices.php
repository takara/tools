<?php

namespace App\Console\Commands\switchbot;

use App\Models\SwitchBotAPI;
use Illuminate\Console\Command;

class devices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sb:devices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'デバイス情報一覧取得';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$res = SwitchBotAPI::getInstance()->getDevices();
		print_r($res);
        return 0;
    }
}
