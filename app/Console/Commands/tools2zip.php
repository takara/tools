<?php

namespace App\Console\Commands;

use App\Models\BookTools;
use Illuminate\Console\Command;

class tools2zip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:2zip {paths*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '指定のディレクトリを圧縮します';

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
        \Log::info("info");
        \Log::error("error");
        $paths = $this->argument("paths");
        foreach($paths as $filename)
        {
            if(is_dir($filename))
            {
                print "[$filename] is directory\n";
                BookTools::deleteUnneededFile($filename);
                $directry=$filename;
                //$zip_filename=str_replace("] ","]",$filename).".zip";
                if(substr($filename,-1)=="/") {
                    $filename=substr($filename,0,-1);
                }

                BookTools::renameCode($filename);

                $zip_filename=BookTools::converOutputZipFilename($filename).".zip";
                // -9 圧縮MAX
                // -j パス保存無し
                $system="zip -9 -j \"{$zip_filename}\" \"{$directry}\"/*";
                $system="zip -9 -j '{$zip_filename}' '{$directry}'/* > /dev/null";
                print "{$system}\n";
                system($system);
            }
        }
        return 0;
    }
}
