<?php

namespace App\Console\Commands;

use App\Models\BookTools;
use Illuminate\Console\Command;

class replace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:replace {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '指定ファイル変更をトレースします';

    /**
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = $this->argument("filename");
		print "$filename\n";
		$debug = BookTools::converOutputZipFilename($filename, ['debug' => true]);
		$ret = BookTools::converOutputZipFilename($filename);
		if ($debug != $ret) {
			$this->alert(" -> 結果が違う！！[$debug][$ret]");
		}
        return 0;
    }
}
