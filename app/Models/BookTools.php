<?php

namespace App\Models;


class BookTools
{
    /**
     * ファイル移動
     */
    public static function moveFile($filename, $movetopath)
    {
        $move_mode = static::getSetting("move_mode", "samba");
        $normalfilename = str_replace("(ipod)", "", $filename);
        switch ($move_mode) {
            case "ssh":
                $ret = TRUE;
                if (strpos($filename, "(ipod)") === FALSE) {
                    $dscptarget = "{$movetopath}";
                } else {
                    $dscptarget = "{$movetopath}ipod/";
                }
                if (!static::copyFileSSH($filename, $dscptarget) === TRUE) {
                    $ret = FALSE;
                } else {
                    unlink($filename);
                }
                break;
            case "samba":
                $system = "mv \"{$normalfilename}\" \"{$movetopath}{$normalfilename}\"";
				static::exec("{$system} > /dev/null");
                $ret = TRUE;
                break;
        }
        return ($ret);
    }

    /**
     * ファイル送信（ssh)
     */
    public static function copyFileSSH($ffilename, $tfilename)
    {
        $fmd5 = md5_file($ffilename);
        $system = "scp \"{$ffilename}\" \"${tfilename}.\"";
		static::exec("{$system}");
        list($host, $dfilename) = explode(":", $tfilename);
        $dfilename .= basename($ffilename);
        $system = "ssh qnap \"/opt/bin/php -r 'print md5_file(\\\"{$dfilename}\\\");'\"";
        $dmd5 = shell_exec("{$system}");
        $ret = ($fmd5 == $dmd5) ? TRUE : FALSE;
        return ($ret);
    }

    public static function isUnneededFile($file)
    {
        if ($file == "www.top-modelz.com" ||
            strpos($file, ".url") !== FALSE ||
            strpos($file, ".txt") !== FALSE ||
            strpos($file, "thumbs.db") !== FALSE ||
            strpos($file, "artofx.org") !== FALSE ||
            strpos($file, ".") === FALSE) // 拡張子無し
        {
            return true;
        }
        return false;
    }

    /**
     * 不要ファイル削除
     */
    public static function deleteUnneededFile($dir)
    {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== FALSE) {
                $file = strtolower($file);
                if (static::isUnneededFile($file)) {
                    $filename = "{$dir}/{$file}";
                    if (substr($dir, -1) == "/") {
                        $filename = "{$dir}{$file}";
                    }
                    echo "   削除 {$filename}\n";
                    unlink("{$filename}");
                }
            }
            closedir($dh);
        }
    }

    /**
     * ファイル名変更
     */
    public static function converOutputZipFilename($filename)
    {
        $ret = preg_replace(
            array(
                "/\] /",
                "/\]_/",
                "/\[成年コミック\]/",
                "/\(成年コミック\)[ ]?/",
                "/\(一般コミック\)[ ]?/",
                "/\(同人誌\)[ ]?/",
                "/\(Adult Manga\)[ ]?/",
                "/\(C[0-9]+\)[ ]?/",
                "/001\./",
            ),
            array(
                "]",
                "]",
                "",
            ),
            $filename);
        return ($ret);
    }

    /**
     * 数字のファイル名に置き換える
     */
    public static function renameCode($path)
    {
        $bExistsNumberFile = FALSE;
        if ($dh = opendir($path)) {
            // すでに数字のファイルがあるかチェック
            while (($file = readdir($dh)) !== FALSE) {
                //print "file[{$file}]\n";
                $file = explode(".", $file);
                if (preg_match("/^[0-9]+$/", $file[0])) {
                    $bExistsNumberFile = TRUE;
                    $file = implode(".", $file);
                    break;
                }
            }
            closedir($dh);
        }
        if (!$bExistsNumberFile) {
            $no = 1;
            if ($dh = opendir($path)) {
                $files = [];
                $cover = [];
                while (($file = readdir($dh)) !== FALSE) {
                    if (substr($file, 0, 1) == ".") {
                        continue;
                    }
                    // カバーを一旦除外しておく
                    if (stripos($file, "cover") !== false) {
                        $cover[] = $file;
                        continue;
                    }
                    $files[] = $file;

                }
                closedir($dh);
                asort($files, SORT_NATURAL);
                // カバーを先頭に持ってくる
                foreach ($cover as $file) {
                    array_unshift($files, $file);
                }
                foreach ($files as $file) {
                    if (static::isUnneededFile($file)) {
                        continue;
                    }
                    $ext = @array_pop(explode(".", $file));
                    $system = sprintf("mv \"{$path}/{$file}\" \"{$path}/%03d.{$ext}\"", $no++);
                    static::exec("{$system} > /dev/null 2>&1");
                }
            }
        } else {
            \Log::info("  すでに数字のファイルがある[{$file}]");
        }
    }

    /**
     * 解凍
     */
    public static function uncompress($filename, $tmpdir = "tmp_dir")
    {
        $arcive_exe_path = getSetting("arcive_exe_path", "/usr/local/bin/"); // "/cygdrive/c/windows/");
        $unzip_cmd = getSetting("unzip_cmd", "unzip");
        /* RAR */
        if (strtolower(substr($filename, -4)) == ".rar") {
            /*
                 -e  書庫のファイルを解凍
                　書庫から１個以上のファイルをカレントディレクトリまたは指定された
                  ディレクトリに解凍します。ただし、書庫中のディレクトリ階層の記録
                  を無視し、すべてのファイルを指定したディレクトリに展開します。

                 -p<password>
                     パスワードを指定します。

                 -q  解凍時の進捗ダイアログを表示しません。
            */
            $uncompresscmd = "{$arcive_exe_path}unrar32 -e -q '{$filename}' {$tmpdir}/ ";
        }
        /* ZIP */
        if (strtolower(substr($filename, -4)) == ".zip") {
            /*
                 -i   解凍状況の表示ダイアログを出す (default)
                      禁止するには --i と指定してください。

                 -P$$$ 暗号化ファイルに対して、$$$ をパスワードとして使用する。
                    暗号化はファイル毎に異なる可能性がありますが、これで指定できるのは
                    全てに共通で１個だけです。

            */
            $uncompresscmd = "{$arcive_exe_path}{$unzip_cmd} -j '{$filename}' -d {$tmpdir}/";
        }
        /* LZH */
        if (strtolower(substr($filename, -4)) == ".lzh") {
            $uncompresscmd = "{$arcive_exe_path}unlha32 x '{$filename}' {$tmpdir}/";
        }
        /* 7-ZIP */
        if (strtolower(substr($filename, -3)) == ".7z") {
            /**/
            $uncompresscmd = "{$arcive_exe_path}7z x -o{$tmpdir}/ '{$filename}'";
        }

        /* 解凍 */
        if ($uncompresscmd) {
            print " ->$uncompresscmd\n";
			static::exec("{$uncompresscmd} > /dev/null");
            $uncompress = TRUE;

            $dh = opendir($tmpdir);
            while (($file = readdir($dh)) !== false) {
                $ext = strtolower(substr($file, -4));
                if (in_array($ext, ['.wmv', '.mp4', '.mpg', '.avi'])) {
                    $system = "mv {$tmpdir}/$file .";
					static::exec($system);
                }
            }
            closedir($dh);
            if (FALSE) {
                // UTF8へ変換
                $system = "convmv -f utf-8 -t cp932 \"{$tmpdir}\"/* --notest";
				static::exec("{$system} > /dev/null 2>&1");
            }
            print " ->delete\n";
            deleteUnneededFile($tmpdir);
            print " ->rename\n";
            renameCode($tmpdir);
        }
    }

    /**
     * 圧縮
     */
    public static function compress($filename, $tmpdir = "tmp_dir")
    {
        $real_dir = str_replace(array("\r", "\n"), "", shell_exec("ls {$tmpdir}"));
        if (is_dir("{$tmpdir}/$real_dir")) {
            $zip_filename = "{$real_dir}.zip";
            if ($norealdir) {
                $zip_filename = substr($filename, 0, -4) . ".zip";
            }
        } else {
            if (!empty($real_dir) && strpos($real_dir, "?") !== FALSE) {
                print "  読み込めないディレクトリが作成されました($real_dir)\n";
                exit;
            }
            $real_dir = "";
            $zip_filename = substr($filename, 0, -4) . ".zip";
        }
        $zip_filename = converOutputZipFilename($zip_filename);
        while (file_exists($zip_filename)) {
            $zip_filename = str_replace(".zip", "_.zip", $zip_filename);
        }
        deleteUnneededFile("{$tmpdir}/{$real_dir}");
        $system = "zip -9 -j '{$zip_filename}' '{$tmpdir}/{$real_dir}'/*";
        print " ->$system\n";
		static::exec("{$system} > /dev/null");
        deleteTempDirectory();
    }

    /**
     * テンポラリディレクトリ削除
     */
    public static function deleteTempDirectory($tmpdir = "tmp_dir")
    {
        $system = "rm -rf \"{$tmpdir}\"";
		static::exec("{$system} > /dev/null 2>&1");
    }

    /**
     * ビデオファイル？
     */
    public static function isVideo($filename)
    {
        $list = array(
            "vid.",
            "-archwayvid",
            "-bgvid",
            "-btsvid",
            "-vid",
            "-wgpvid",
        );
        $ret = FALSE;
        foreach ($list as $word) {
            if (strpos($filename, $word) !== FALSE) {
                $ret = TRUE;
                break;
            }
        }
        return ($ret);
    }

    /**
     * 解凍ディレクトリビデオファイルチェック
     *
     * 画像の圧縮であれば１０枚以上あるはず
     * かつ動画ファイルがあればビデオの圧縮とみなす
     */
    public static function isTempVideoCheck($tmpdir = "tmp_dir")
    {
        $system = "ls {$tmpdir}";
        $res = explode("\n", shell_exec($system));
        if (count($res) > 10 || count($res) - 1 < 1) {
            // １０枚以上あるので動画ではない
            return (FALSE);
        }
        //print $res;
        //print count(explode("\n",$res))."\n";
        $list = array(
            ".mp4",
            ".wmv",
            ".avi",
        );
        $ret = FALSE;
        foreach ($list as $word) {
            foreach ($res as $filename) {
                if (strpos(strtolower($filename), $word) !== FALSE) {
                    $ret = TRUE;
                }
            }
        }
        return ($ret);
    }

    /**
     * 変換済みかチェック
     */
    public static function isNoConvert($filename)
    {
        $list = array(
            "(ipod)",
        );
        $ret = FALSE;
        foreach ($list as $word) {
            if (strpos($filename, $word) !== FALSE) {
                $ret = TRUE;
                break;
            }
            if (file_exists(substr($filename, 0, -4) . $word . substr($filename, -4))) {
                $ret = TRUE;
                break;
            }
        }
        return ($ret);
    }

    /**
     * テンポラリディレクトリ取得
     */
    public static function getTempDirectory()
    {
        return sys_get_temp_dir();
    }

    /**
     * デフォルト付き設定取得
     */
    public static function getSetting($name, $default = "")
    {
        $ret = get_cfg_var($name);
        if (empty($ret)) {
            $ret = $default;
        }
        return ($ret);
    }
    public static function moveTrash(string $filename)
    {
        $home = static::getHome();
        static::moveFile($filename, "{$home}/.Trash/");
    }

    public static function getHome() :string
    {
        return env("HOME");
    }

    public static function exec(string $cmd)
    {
        $pwd = getcwd();
        \Log::info(__METHOD__."():".__LINE__.":{$pwd}:{$cmd}");
        return system($cmd);
    }
}
