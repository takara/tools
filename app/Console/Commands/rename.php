<?php

namespace App\Console\Commands;

use App\Models\BookTools;
use Illuminate\Console\Command;

class rename extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:rename {--f|format=第%02d巻 : リネームのフォーマット} {--d|dry-run : ドライラン} {paths*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '指定フォーマットでリネームします';

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
        $paths = $this->argument("paths");
        $dryRun = $this->option("dry-run");
        $format = $this->option("format");
        if (strpos($format, "%") === false) {
            $format .= " 第%02d巻";
        }
        foreach($paths as $filename)
        {
            $this->info("$filename");
            if (preg_match("/^.*[^0-9]([0-9]+).*$/", $filename, $match) === false) {
                //print __METHOD__ . "():" . __LINE__ . ":\n";
                // 数字だけの場合
                if (preg_match("/^([0-9]+).*?$/",$filename,$match) === false) {
                    continue;
                }
            } else {
                //print __METHOD__."():".__LINE__.":".print_r($match)."\n";
            }
            $no = (int)($match[1]??$match);
            $fmt = $format;
            // 拡張子が無ければ補完
            if ($this->existExtension($fmt) === false) {
                $info = pathinfo($filename);
                $ext = $info['extension'] ?? "";
                if (empty($ext) === false) {
                    $fmt .= ".$ext";
                }
            }
            $rename = BookTools::converOutputZipFilename(sprintf($fmt, $no));
            $this->line(" -> {$rename}に変更");
            $cmd ="mv '$filename' '$rename'";
            if ($dryRun) {
                $this->warn(" -> {$cmd}");
            } else {
                BookTools::exec($cmd);
            }
        }
        return 0;
    }

    protected function existExtension(string $path)
    {
        $info = pathinfo($path);
        return isset($info['extension']);

    }
}
