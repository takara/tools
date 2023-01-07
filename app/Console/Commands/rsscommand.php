<?php

namespace App\Console\Commands;

use App\Models\Rss;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Log;

class rsscommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:rss';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'RSSを収録し通知する';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        Log::debug("RSS実行");

        /** @var Repository $config */
        $config = app('config');
        $rssUrl = $config->get('app.rss.url');
        try {
            $rss = simplexml_load_file($rssUrl);
        } catch (Exception $e) {
            Log::debug("RSS読込エラー[".$e->getMessage()."]");
            return 0;
        }
        Log::debug("RSS読込完了");

		$list = '';

		foreach ($rss->channel->item as $item ) {
			//$list .=  $item->link;
			$list .=  "$item->title($item->link)\n";
			$link = urldecode($item->link);
			$title = str_replace("[", "\\[", $item->title);

            /** @var Illuminate\Database\Eloquent\Collection $data */
			$data = Rss::where(['url' => $link])->first();
			if (empty($data)) {
				$data = new Rss([
					'url' => $link,
					'title' => $item->title,
				]);
				$data->save();
				Log::info("新規[$item->title]");
				//system("/usr/bin/osascript -e 'display notification \"$link\" with title \"{$item->title}\"'");
			} else {
				if ($data->title != $item->title) {
					//\Log::info("更新[{$item->title}][{$data->title}]");
					if ($data->notice) {
						//system("/usr/bin/osascript -e 'display notification \"更新\" with title \"{$item->title}\" sound name \"てやんでー\"'");
						system("/opt/homebrew/bin/terminal-notifier -title \"更新\" -message \"$title\" -sound \"てやんでー\" -open \"$item->link\"");
					}
					$data->touch();
					$data->title = $item->title;
					$data->save();
				} else {
					Log::debug("変更無し[$item->title][$data->title]");
					//system("/opt/homebrew/bin/terminal-notifier -title \"{$item->title}\" -sound name \"てやんでー\"");
					//system("/opt/homebrew/bin/terminal-notifier -title \"変更無し\" -message \"{$title}\" -sound \"てやんでー\" -open \"{$item->link}\"");
				}
			}
		}


		//echo $list;
        return 0;
    }
}
