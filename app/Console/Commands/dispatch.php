<?php

namespace App\Console\Commands;

use App\Models\BookTools;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class dispatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:dispatch {filename : 処理ファイル}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ファイル名により処理します';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		\Log::debug("version:".phpversion());
		\Log::debug("extension_dir:".get_cfg_var("extension_dir"));
		$this->info("dispatch");
        $filename = $this->argument("filename");
		\Log::debug($filename);
		if (file_exists($filename) === false) {
			if (file_exists($filename.".part")) {
				\Log::debug("{$filename}はダウンロード中");
				return 0;
			} else {
				$this->error("{$filename}が存在しません");
				\Log::debug("{$filename}が存在しません");
				return 1;
			}
		}
		$info = pathinfo($filename);
		if (isset($info['extension']) === false) {
			return 0;
		}
		$ext  = strtolower($info['extension']);;
		$method = Str::camel("dispatch_extension_$ext");
		if (method_exists($this, $method)) {
			$this->$method($filename);
		} else {
			\Log::error("method[$method]がない");
		}
        return 0;
    }

	protected function dispatchExtensionMp4(string $filename)
	{
	}
	
	protected function dispatchExtensionRar(string $filename)
	{
		$extList = BookTools::checkRarFile($filename);
		$keys = array_keys($extList);
		$maxExt = reset($keys);
		\Log::debug("maxExt[$maxExt]");
		if (BookTools::isPicture($maxExt)) {
			$cmd = "rezip \"{$filename}\"";
			$this->line(" ->$cmd");
			BookTools::exec($cmd);
		}
	}

	protected function dispatchExtensionPng(string $filename)
	{
		$cmd = "~/.bin/png2jpg $filename";
		BookTools::exec($cmd);
		$jpgfile = str_replace("png", "jpg", $filename);
		if (file_exists($jpgfile) && filesize($jpgfile)) {
			BookTools::moveTrash($filename);
		}
	}
}
