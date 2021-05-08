<?php

namespace App\Console\Commands;

use App\Models\BookTools;
use Illuminate\Console\Command;

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
        $tmpdir=tempnam(BookTools::getTempDirectory(), "rezip_");
        $arcive_exe_path = BookTools::getSetting("arcive_exe_path", "/usr/local/bin/"); // "/cygdrive/c/windows/");
        $unzip_cmd		 = BookTools::getSetting("unzip_cmd","unzip");
        $unrar_cmd		 = BookTools::getSetting("unrar_cmd","unrar");
        $norealdir = $this->option("norealdir");
        foreach($paths as $filename)
        {
            $uncompress=FALSE;
            $uncompresscmd="";
            // 作業ディレクトリが残っている？
            if(file_exists($tmpdir))
            {
                //print "作業ディレクトリ残っているため、削除\n";
                system("rm -rf '$tmpdir' > /dev/null");
                system("mkdir '$tmpdir' > /dev/null");
                $tmpdir .= "/";
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
                print "$filename\n";
                print " ->$uncompresscmd\n";
                system("{$uncompresscmd} > /dev/null");
                $uncompress=TRUE;

                $dh = opendir($tmpdir);
                while(($file = readdir($dh)) !== false) {
                    $ext = strtolower(substr($file, -4));
                    if (in_array($ext, ['.wmv', '.mp4', '.mpg', '.avi', '.m4v'])) {
                        printf("動画ファイル[$file]\n");
                        $system = "mv {$tmpdir}/$file .";
                        system($system);
                        $uncompress=false;
                    }
                }
                closedir($dh);
                if(FALSE)
                {
                    // UTF8へ変換
                    $system="convmv -f utf-8 -t cp932 \"{$tmpdir}\"/* --notest";
                    system("{$system} > /dev/null 2>&1");
                }
                print " ->rename\n";
                BookTools::renameCode($tmpdir);
            }

            /* 圧縮 */
            if($uncompress)
            {
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
                        print "読み込めないディレクトリが作成されました($real_dir)\n";
                        return 1;
                    }
                    $real_dir="";
                    $zip_filename=substr($filename,0,-4).".zip";
                }
                $zip_filename=BookTools::converOutputZipFilename($zip_filename);
                if($zip_filename==$filename)
                {
                    $zip_filename=str_replace(".zip","_.zip",$zip_filename);
                }
                $deldir = "{$tmpdir}/{$real_dir}";
                if (substr($tmpdir,-1) == "/") {
                    $deldir = "{$tmpdir}{$real_dir}";
                }
                BookTools::deleteUnneededFile("{$deldir}");
                $system="zip -9 -j '{$zip_filename}' '{$tmpdir}/{$real_dir}'/*";
                print " ->$system\n";
                system("{$system} > /dev/null");
                $system="rm -rf \"{$tmpdir}\"";
                //print "$system\n";
                system("{$system} > /dev/null 2>&1");
                if (file_exists($zip_filename) && filesize($zip_filename)) {
                    BookTools::moveTrash($filename);
                }
            }
        }
        return 0;
    }
}
