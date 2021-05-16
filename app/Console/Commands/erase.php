<?php

namespace App\Console\Commands;

use App\Models\BookTools;
use Illuminate\Console\Command;

class erase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:erase {paths*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '指定ファイルをすべてゴミ箱に入れます';

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
        $home  = env("HOME");
        $trash = "{$home}/.Trash/";
        foreach($paths as $filename)
        {
            $this->info("$filename");
            BookTools::moveTrash($filename);
        }
        return 0;
    }
}
