<?php

namespace App\Console\Commands;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Console\Command;

class tweet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:tweet';

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
		$consumer_key         = app('config')->get('app.consumer_key');
		$consumer_key_sercret = app('config')->get('app.consumer_key_sercret');
		$access_token         = app('config')->get('app.access_token');
		$access_token_secret  = app('config')->get('app.access_token_secret');

		$connection = new TwitterOAuth($consumer_key, $consumer_key_sercret, $access_token, $access_token_secret);
		$tweets = $connection->get('search/tweets', ['q' => 'taka2065jp']);
		foreach ($tweets->statuses as $tweet) {
			//print_r($tweet);
			print "[{$tweet->text}]\n";
			//$this->line($tweet['text']);
		}
		print $tweets->statuses[0]->text;
		//print_r(get_object_vars($tweets));
		//print_r($tweets->0);
        return 0;
    }
}
