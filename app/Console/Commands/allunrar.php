<?php

namespace App\Console\Commands;

use App\Models\BookTools;
use Illuminate\Console\Command;

class allunrar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:allunrar {paths*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '指定rarファイルをすべて解凍します';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $paths           = $this->argument("paths");
        $tmpdir          = tempnam(BookTools::getTempDirectory(), "rezip_");
        $arcive_exe_path = BookTools::getSetting("arcive_exe_path", "/usr/local/bin/"); // "/cygdrive/c/windows/");
        $unzip_cmd		 = BookTools::getSetting("unzip_cmd","unzip");
        $unrar_cmd		 = BookTools::getSetting("unrar_cmd","unrar");
        foreach($paths as $filename)
        {
            $this->info("$filename");
            $cmd = "{$unrar_cmd} x '{$filename}'";
            $this->line(" -> $cmd");
            BookTools::exec($cmd);
        }
        return 0;
    }
}
