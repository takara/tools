<?php

namespace App\Console\Commands;

use App\Models\BookTools;
use Illuminate\Console\Command;

/**
 * 圧縮ファイルをzipで圧縮し直す
 *
 * @see https://stillat.com/blog/2016/12/03/custom-command-styles-with-laravel-artisan
 * @package App\Console\Commands
 */
class rezip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:rezip {--a|norealdir : ディレクトリ名を書庫名に使わない} {paths*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '圧縮ファイルをzipで圧縮し直す';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$this->info("tools:rezip");
        $paths           = $this->argument("paths");
        $tmpdir          = tempnam(BookTools::getTempDirectory(), "rezip_");
        $arcive_exe_path = BookTools::getSetting("arcive_exe_path", "/usr/local/bin/"); // "/cygdrive/c/windows/");
        $unzip_cmd		 = BookTools::getSetting("unzip_cmd","unzip");
        $unrar_cmd		 = BookTools::getSetting("unrar_cmd","unrar");
        $norealdir       = $this->option("norealdir");
        foreach($paths as $filename)
        {
			\Log::info(__METHOD__."():".__LINE__.":$filename");
            $uncompress=FALSE;
            $uncompresscmd="";
            // 作業ディレクトリが残っている？
            if(file_exists($tmpdir))
            {
                \Log::info("作業ディレクトリ残っているため、削除");
				BookTools::exec("rm -rf '$tmpdir'");
				BookTools::exec("mkdir '$tmpdir'");
                $tmpdir .= "/";
            }
            $this->line("[$filename]");

            if (file_exists($filename) === false) {
                $this->error(" -> ファイルが存在しません");
                continue;
            }

            /* RAR */
            if(strtolower(substr($filename,-4))==".rar") {
                /*
                     -e  書庫のファイルを解凍
                    　書庫から１個以上のファイルをカレントディレクトリまたは指定された
                      ディレクトリに解凍します。ただし、書庫中のディレクトリ階層の記録
                      を無視し、すべてのファイルを指定したディレクトリに展開します。

                     -p<password>
                         パスワードを指定します。

                     -q  解凍時の進捗ダイアログを表示しません。
                */
                $uncompresscmd="{$arcive_exe_path}{$unrar_cmd} e '{$filename}' {$tmpdir}/ ";
            }

            /* ZIP */
            if(strtolower(substr($filename,-4))==".zip") {
                /*
                     -i   解凍状況の表示ダイアログを出す (default)
                          禁止するには --i と指定してください。

                     -P$$$ 暗号化ファイルに対して、$$$ をパスワードとして使用する。
                        暗号化はファイル毎に異なる可能性がありますが、これで指定できるのは
                        全てに共通で１個だけです。

                */
                $uncompresscmd="{$arcive_exe_path}{$unzip_cmd} -j '{$filename}' -d {$tmpdir}/";
            }

            /* LZH */
            if(strtolower(substr($filename,-4))==".lzh")
            {
                $uncompresscmd="{$arcive_exe_path}unlha32 x '{$filename}' {$tmpdir}/";
            }

            /* 7-ZIP */
            if(strtolower(substr($filename,-3))==".7z")
            {
                /**/
                $uncompresscmd="{$arcive_exe_path}7z x -o{$tmpdir}/ '{$filename}'";
            }

            /* 解凍 */
            if($uncompresscmd)
            {
                $this->line(" ->$uncompresscmd");
				BookTools::exec("{$uncompresscmd}");
                $uncompress=TRUE;

                $dh = opendir($tmpdir);
                while(($file = readdir($dh)) !== false) {
                    $ext = strtolower(substr($file, -4));
                    if (in_array($ext, ['.wmv', '.mp4', '.mpg', '.avi', '.m4v'])) {
                        $this->warn("動画ファイル[$file]");
                        $system = "mv {$tmpdir}/$file .";
						BookTools::exec($system);
                        $uncompress=false;
                    }
                }
                closedir($dh);
                if(FALSE)
                {
                    // UTF8へ変換
                    $system="convmv -f utf-8 -t cp932 \"{$tmpdir}\"/* --notest";
					BookTools::exec("{$system}");
                }
                $this->line(" ->rename");
                BookTools::renameCode($tmpdir);
            }

            /* 圧縮 */
            if($uncompress)
            {
                \Log::info(__METHOD__."():".__LINE__.":");
                $real_dir=str_replace(array("\r","\n"),"",shell_exec("ls {$tmpdir}"));
                if(is_dir("{$tmpdir}/$real_dir"))
                {
                    $zip_filename="{$real_dir}.zip";
                    if($norealdir)
                    {
                        $zip_filename=substr($filename,0,-4).".zip";
                    }
                } else {
                    if(!empty($real_dir) && strpos($real_dir,"?")!==FALSE)
                    {
                        $this->line("読み込めないディレクトリが作成されました($real_dir)");
                        return 1;
                    }
                    $real_dir="";
                    $zip_filename=substr($filename,0,-4).".zip";
                }
                $zip_filename = BookTools::converOutputZipFilename($zip_filename);
                if($zip_filename == $filename)
                {
                    $zip_filename=str_replace(".zip","_.zip",$zip_filename);
                }
                $deldir = "{$tmpdir}/{$real_dir}";
                if (substr($tmpdir,-1) == "/") {
                    $deldir = "{$tmpdir}{$real_dir}";
                }
                BookTools::deleteUnneededFile("{$deldir}");
                $system = "zip -9 -j '{$zip_filename}' '{$tmpdir}/{$real_dir}'/*";
                $this->line(" ->$system");
				BookTools::exec("{$system}");
                $system="rm -rf \"{$tmpdir}\"";
				BookTools::exec("{$system}");
                if (file_exists($zip_filename) && filesize($zip_filename)) {
                    BookTools::moveTrash($filename);
                }
            }
        }
        return 0;
    }
}
