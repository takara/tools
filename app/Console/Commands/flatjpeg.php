<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BookTools;

class flatjpeg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:flatjpeg';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() : int
    {
		$res = BookTools::findFiles();
		foreach ($res as $filename) {
			$info = pathinfo($filename);
			if (!isset($info['extension']) || strtolower($info['extension']) != "jpg") {
				continue;
			}
			$toname = str_replace("/", "_", $filename);
			print "mv '$filename' '$toname'\n";
		}
        return 0;
    }
}
