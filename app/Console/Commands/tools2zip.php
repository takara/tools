<?php

namespace App\Console\Commands;

use App\Models\BookTools;
use Illuminate\Console\Command;
use Log;

class tools2zip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:2zip {--c|norename : リネーム無し} {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '指定のディレクトリを圧縮します';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$norename = $this->option("norename");
        $path = $this->argument("path");
		$paths = BookTools::getFiles($path);
        foreach($paths as $filename)
        {
            if(is_dir($filename) === false) {
                Log::info("ディレクトではないのでスキップ[{$filename}]");
                continue;
            }
			if ($filename == "." || $filename == "..") {
				continue;
			}
            $this->info($filename);
            $this->line(" -> [$filename] is directory");
            BookTools::deleteUnneededFile($filename);
            $directry=$filename;
            if(substr($filename,-1)=="/") {
                $filename=substr($filename,0,-1);
            }

			if (!$norename) {
				BookTools::renameCode($filename);
			}

            $zip_filename=BookTools::converOutputZipFilename($filename).".zip";
            // -9 圧縮MAX
            // -j パス保存無し
            $system="zip -9 -j '{$zip_filename}' '{$directry}'/*";
            BookTools::exec($system);
            if (file_exists($zip_filename) && filesize($zip_filename)) {
                BookTools::moveTrash($filename);
            }
        }
        return 0;
    }
}
