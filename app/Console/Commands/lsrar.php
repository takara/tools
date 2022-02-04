<?php

namespace App\Console\Commands;

use App\Models\BookTools;
use Illuminate\Console\Command;

class lsrar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:lsrar {paths*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '指定rarファイルを中身を表示します';

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
        foreach($paths as $filename)
        {
            $this->info("$filename");
            $rar_arch = \RarArchive::open($filename);
            if ($rar_arch === FALSE) {
                die("Could not open RAR archive.");
            }
            $rar_entries = $rar_arch->getEntries();
            if ($rar_entries === FALSE)
                die("Could not retrieve entries.");

            echo "Found " . count($rar_entries) . " entries.\n";

            foreach ($rar_entries as $e) {
                echo $e;
                echo "\n";
            }

            $rar_arch->close();
        }
        return 0;
    }
}
