<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Scraping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:scraping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		//$goutte = \Goutte::request('GET', 'http://13dl.net/%e3%82%b0%e3%83%a9%e3%83%b3%e3%83%89%e3%82%b8%e3%83%a3%e3%83%b3%e3%83%9715.html');
		//$goutte = \Goutte::request('GET', 'https://www.yahoo.co.jp/');
		//$goutte = \Goutte::request('GET', 'https://www.google.co.jp/');
		$goutte = \Goutte::request('GET', 'https://www.amazon.co.jp/');
		// #post-214419 > header > h1
		dd($goutte);
        $goutte->filter('h1')->each(function ($h1) {
                dd($h1);
        });
    }
}
