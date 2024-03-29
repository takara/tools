<?php

namespace App\Models;

use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class BookTools
{
    /**
     * ファイル名変更
     */
    public static function converOutputZipFilename($filename, array $opt = [])
    {
		$debug = $opt['debug'] ?? false;
        $list = ReplaceKeyword::all();
		if ($debug) {
			$ret = $filename;
			foreach ($list as $row) {
				$pattern = "/{$row->pattern}/";
				$replacement = $row->keyword;
				$from = $ret;
				static::info("  patten[{$pattern}]");
				$ret = preg_replace($pattern, $replacement, $ret);
				static::line("   ->{$from} -> {$ret}");
			}
		} else {
			$pattern = [];
			$replacement = [];
			foreach ($list as $row) {
				$pattern[] = "/{$row->pattern}/";
				$replacement[] = $row->keyword;
			}
			$ret = preg_replace($pattern, $replacement, $filename);
		}
        return ($ret);
    }

    /**
     * @var Command
     */
    protected static $cmd = null;

	/**
	 * jpegファイルをフラットなファイル名にする
	 */
    public static function flatjpeg($path = ".")
	{
		if (substr($path, -1) != "/") {
			$path .= "/";
		}
		$res = static::findFiles($path);
		foreach ($res as $filename) {
			$info = pathinfo($filename);
			if (!isset($info['extension']) || strtolower($info['extension']) != "jpg") {
				continue;
			}
			$toname = str_replace("/", "_", $filename);
			$system="mv '$filename' '$path$toname'";
			static::exec("{$system}", ['log' => false]);
		}
	}

    public static function findFiles($path = ".") : array
	{
		$ret = [];
		$dh = opendir($path);
		if ($dh === false) {
			\Log::error("{$pwd}のオープンに失敗しました");
			return $ret;
		}
		while (($file = readdir($dh)) !== false) {
			if (substr($file, 0, 1) == ".") {
				continue;
			}
			$filename = "$path/$file";
			//print "$filename\n";
			if (is_dir($filename)) {
				$res = static::findFiles($filename);
				$ret =array_merge($ret, $res);
			}
			$ret[] = preg_replace("/^\.\//", "", $filename);
		}
		return $ret;
	}

    public static function getFiles($pattern) : array
	{
		$ret = [];
		$pwd = getcwd();
		\Log::info("{$pwd}");
		if (file_exists($pattern)) {
			return [$pattern];
		}
		$dh = opendir($pwd);
		if ($dh === false) {
			\Log::error("{$pwd}のオープンに失敗しました");
			return $ret;
		}
		\Log::info(__METHOD__."():".__LINE__.":");
		while (($file = readdir($dh)) !== false) {
			if (!fnmatch($pattern, $file)) {
				\Log::info(__METHOD__."():".__LINE__.":skip [$file]");
				continue;
			}
			\Log::debug($file);
			$ret[] = $file;
		}
		closedir($dh);
		\Log::info(__METHOD__."():".__LINE__.":");
		return $ret;
	}

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
				static::exec("{$system}");
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
        $system = "ssh qnap \"/opt/bin/php -r 'echo md5_file(\\\"{$dfilename}\\\");'\"";
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
					if (is_dir($filename)) {
						//rmdir("{$filename}");
					} else {
						unlink("{$filename}");
					}
                }
            }
            closedir($dh);
        }
    }

    public static function renameFormat(string $filename, bool $exec = true): string
	{
		$ret = $filename;
		$list = get_class_methods(static::class);
		foreach ($list as $method) {
			if (preg_match('/is_([a-z0-9]+_file_type)/', Str::snake($method), $match)) {
				if (static::$method($filename)) {
					$renameMethod = Str::camel("rename_{$match[1]}");
					$ret = static::$renameMethod($filename);
				}
			}
		}
		if ($exec && $filename != $ret) {
			$system = "mv \"{$filename}\" \"{$ret}\"";
			static::exec("{$system}");
		}
		return $ret;
	}

	public static function renameFc2FileType($filename): string
	{
		if (preg_match("/([0-9]+)\-([0-9])/", $filename, $match)) {
			$info = pathinfo($filename);
			$dir = $info['dirname'] == "/" ? "" : $info['dirname'];
			return sprintf("%s/FC2-PPV-%d-%d.%s", 
				$dir,
				$match[1],
				$match[2],
				$info['extension']);
		}
		if (preg_match("/([0-9]{4,10})/", $filename, $match)) {
			$info = pathinfo($filename);
			$dir = $info['dirname'] == "/" ? "" : $info['dirname'];
			return sprintf("%s/FC2-PPV-%d.%s", 
				$dir,
				$match[1], $info['extension']);
		}
		return $filename;
	}

	public static function isFc2FileType($filename): bool
	{
		if (preg_match("/(fc2)/i", $filename, $match) == false) {
			return false;
		}
		if (preg_match("/ppv/i", $filename) == false) {
			return false;
		}
		if (preg_match("/[0-9]{2,10}/", $filename) == false) {
			return false;
		}
		return true;
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
                natsort($files);
				$files = static::sortPages($files);
				\Log::error(__METHOD__."():".__LINE__.":".json_encode($files));
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
                    static::exec("{$system}");
                }
            }
        } else {
            \Log::info("  すでに数字のファイルがある[{$file}]");
        }
    }

	public static function sortPages(array $files): array
	{
		usort($files, function($a, $b) {
			if (preg_match_all("/([0-9]+)/", $a, $match)) {
				$a = (int)array_pop($match[1]);
			} else {
				$a = 0;
			}
			if (preg_match_all("/([0-9]+)/", $b, $match)) {
				$b = (int)array_pop($match[1]);
			} else {
				$b = 0;
			}
			return $a - $b;
		});
		return $files;
	}

	public static function checkRarFile(string $filename) : array
	{
		if (extension_loaded("rar") === false) {
			if (dl("rar") === false) {
				\Log::error("rar 拡張モジュールがロードされていない");
				return [];
			}
		}
		$rar_arch = \RarArchive::open($filename);
		if ($rar_arch === FALSE)
		{
			throw new \Exception("RARファイル($filename)を開けません");
		}

		$rar_entries = $rar_arch->getEntries();
		if ($rar_entries === FALSE)
		{
			throw new \Exception("Could not retrieve entries.");
		}

		$ret = [];
		foreach ($rar_entries as $e) {
			$name = $e->getName();
			if ($e->isDirectory() && strpos($name, "/") !== false) {
				$ret["Two-tier path"] = 0;
			}
			$info = pathinfo($name);
			if (isset($info['extension']) === false) {
				continue;
			}
			$ext = strtolower($info['extension']);
			if (isset($ret[$ext])) {
				$ret[$ext]++;
			} else {
				$ret[$ext] = 1;
			}
		}
		$rar_arch->close();
		arsort($ret);
		return $ret;
	}

	public static function isPicture(string $ext) : bool
	{
		switch(strtolower($ext))
		{
		case 'jpg':
		case 'jpeg':
		case 'png':
		case 'gif':
			$ret = true;
			break;
		default:
			$ret = false;
			break;
		}
		return $ret;
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
            static::line(" ->$uncompresscmd");
			static::exec("{$uncompresscmd}");
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
				static::exec("{$system}");
            }
            static::line(" ->delete");
            static::deleteUnneededFile($tmpdir);
            static::line(" ->rename");
            static::renameCode($tmpdir);
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
                static::error("  読み込めないディレクトリが作成されました($real_dir)");
                exit;
            }
            $real_dir = "";
            $zip_filename = substr($filename, 0, -4) . ".zip";
        }
        $zip_filename = static::converOutputZipFilename($zip_filename);
        while (file_exists($zip_filename)) {
            $zip_filename = str_replace(".zip", "_.zip", $zip_filename);
        }
        static::deleteUnneededFile("{$tmpdir}/{$real_dir}");
        $system = "zip -9 -j '{$zip_filename}' '{$tmpdir}/{$real_dir}'/*";
        static::line(" ->$system\n");
		static::exec("{$system}");
        static::deleteTempDirectory();
    }

    /**
     * テンポラリディレクトリ削除
     */
    public static function deleteTempDirectory($tmpdir = "tmp_dir")
    {
        $system = "rm -rf \"{$tmpdir}\"";
		static::exec("{$system}");
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
        if (file_exists($filename) === false) {
            $this->error(" ->{$filename}が見つかりません");
            return;
        }
        static::warn(" -> [{$filename}]をゴミ箱へ");
        \Log::info(" -> [{$filename}]をゴミ箱へ");
        $filename = realpath($filename);
        $cmd =
            "osascript -e \"\"\"\n".
            "tell application \\\"Finder\\\"\n".
            "move POSIX file \\\"{$filename}\\\" to trash\n".
            "end tell\n".
            "\"\"\"".
            "";
        static::exec($cmd, ['log' => false]);

    }

    public static function notice(string $message="")
    {
        $cmd = "osascript -e 'display notification \"hogehoge\" with title \"Fuga\"'";
        static::exec($cmd, ['log' => false]);

    }
    public static function getHome() :string
    {
        return env("HOME");
    }

    public static function exec(string $cmd, array $opt = [])
    {
		$enableLog = $opt['log'] ?? true;
        $pwd = getcwd();
		if ($enableLog) {
			static::line(" -> $cmd");
			\Log::debug(__METHOD__."():".__LINE__.":pwd[{$pwd}]:cmd[{$cmd}]");
		}
        if (strpos($cmd,">") !== false) {
            \Log::info(" ->リダイレクト指定");
        } else {
            $cmd .= " 2>&1 > /dev/null";
        }
        //static::info($cmd);
        return system($cmd);
    }

    protected static function getOutPutObject()
    {
        if (is_null(static::$cmd)) {
            static::$cmd = new Command();
            $out = new \Symfony\Component\Console\Output\ConsoleOutput();
            $in  = new \Symfony\Component\Console\Input\ArgvInput();
            static::$cmd->setOutput(new OutputStyle($in, $out));
        }
        return static::$cmd;
    }

    public static function __callStatic(string $name ,array $arguments)
    {
        $ouputList = [
            "line",
            "info",
            "warn",
            "error",
            "alert",
        ];
        if (in_array($name, $ouputList) === false) {
            throw new \Exception("未定義のメソッド($name)です");
        }
        if (config("app.env") == "testing") {
            return;
        }
        $cmd = static::getOutPutObject();
        call_user_func_array([$cmd, $name], $arguments);
    }
}
