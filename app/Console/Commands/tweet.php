<?php

namespace App\Console\Commands;

use App\Models\Twitter;
use Illuminate\Console\Command;

class tweet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:tweet {cmd} {--i|id=null : 記事ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$param = [];
		$cmd = $this->argument("cmd");
		$id = $this->option("id");
		if (!empty($id)) {
			$param += ['id' => $id];
		}
		if (method_exists($this, $cmd) === false) {
			$this->error("cmd($cmd)は存在しません");
			return 1;
		}
		$this->$cmd($param);
        return 0;
    }

	protected function test(array $opt = [])
	{
		$twetter = new Twitter();
		$res = $twetter->getFolloers("taka2065jp");
		print_r($res);
	}
	
	protected function iine(array $opt = [])
	{
		$id = $opt['id'] ?? null;
		if (is_null($id)) {
			throw new \Exception("idが指定されていません");
		}
		$twetter = new Twitter();
		$res = $twetter->getRetweet($id);
		print_r($res);
	}

	protected function repry(array $opt = [])
	{
		$id = $opt['id'] ?? null;
		if (is_null($id)) {
			throw new \Exception("idが指定されていません");
		}
		$twetter = new Twitter();
		$res = $twetter->getLikingUsers($id);
		foreach($res as $name) {
			$url = "https://twitter.com/{$name}";
			system("open $url");
		}
	}

	protected function now(array $opt = [])
	{
		$twetter = new Twitter();
		$iineList = $twetter->getFavorites("taka2065jp", ['count' => 200]);
		$res = $twetter->search("#マイクラ", 100);
		foreach($res->statuses as $tweet) {
			$id = $tweet->id;
			$name = $tweet->user->screen_name;
			$url = "https://twitter.com/{$name}/status/{$id}";
			$text = $tweet->text;
			if (strpos($text, "RT ") !== false) {
				continue;
			}
			if (isset($tweet->entities->media) === false) {
				continue;
			}
			if (in_array($id, $iineList)) {
				//$this->line("いいねされてた");
				continue;
			}
			if ($name == "taka2065jp") {
				continue;
			}
			system("open $url");
			//print str_repeat("-",80)."\n";
			//print "{$url}\n";
			//print "{$text}\n";
		}
	}
}
