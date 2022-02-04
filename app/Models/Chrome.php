<?php


namespace App\Models;


use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;

class Chrome
{
    /**
     * @var Command
     */
    protected static $cmd = null;

    /**
     * checksum
     * toots
     *   bookmark_bar
     *     children
     *       children
     *         children
     *   other
     *   synced
     * sync_metadata
     * version
     */
    public function checkBookmark()
    {
        /*
        $this->line($this->checkURL("https://www.google.com/"));
        $this->line($this->checkURL("https://www.google.com/aa.html"));
        return;
        */
        $home = env("HOME");
        $filename = "{$home}/Library/Application Support/Google/Chrome/Default/Bookmarks";
        //$this->line($filename);
        $bookmark = file_get_contents($filename);
        $json = json_decode($bookmark, true);
        //var_dump(array_keys($json['roots']['other']));
        $this->check($json,"roots/bookmark_bar");
    }

    protected function checkURL($url)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HEADER            => true,
            CURLOPT_NOBODY            => true,
            CURLOPT_FOLLOWLOCATION    => false,
            CURLOPT_CONNECTTIMEOUT    => 100,
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_SSL_VERIFYPEER    => false,
            CURLOPT_TIMEOUT           => 100
        ]);
        curl_exec($ch);
        $ret = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $ret;
    }

    protected function check(array $json, string $path, $fname = "")
    {
        $cur = $this->moveArray($json, $path);
        $space = $this->getSpece($path);
        if (is_array($cur)) {
            $keys = array_keys($cur);
            $key = reset($keys);
            if (is_numeric($key)) {
                $list = $cur;
                foreach ($list as $idx => $cur) {
                    if (is_array($cur)) {
                        static::check($json, "{$path}/{$idx}", $fname);
                    }
                }
            } else {
                if (isset($cur['type'])) {
                    switch ($cur['type']) {
                        case "folder":
                            $fname = "{$fname}/{$cur['name']}";
                            //$this->line("{$space}[{$fname}]");
                            static::check($json, "{$path}/children", $fname);
                            break;
                        case "url":
                            $sts = $this->checkURL($cur['url']);
                            if ($this->isOKURL($sts)) {
                                $this->line("{$space}sts[$sts]{$cur['name']}({$cur['url']})");
                            } else {
                                $this->warn("{$space}sts[$sts][$fname]{$cur['name']}({$cur['url']})");
                            }
                            break;
                        default:
                            $this->line($cur['type']);
                            break;
                    }
                }
            }
        }
    }

    protected function isOKURL($sts)
    {
        return in_array($sts,[200,301,302,308,405]);
    }

    protected function moveArray(array $ary, string $path)
    {
        $ret = $ary;
        $paths = explode("/", $path);
        foreach ($paths as $idx) {
            $ret = $ret[$idx];
        }
        return $ret;
    }

    protected function getSpece($path)
    {
        $paths = explode("/", $path);
        return str_repeat(" ", count($paths));

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

    public function __call(string $name ,array $arguments)
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
