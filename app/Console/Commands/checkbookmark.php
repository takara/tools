<?php

namespace App\Console\Commands;

use App\Models\BookTools;
use App\Models\Chrome;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;

class checkbookmark extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:checkbookmark';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'chromeのbookmarkをチェックしますします';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $chrome = new Chrome();
        $chrome->checkBookmark();
    }
}
