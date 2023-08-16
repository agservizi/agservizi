<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DbLoadBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:load-backup';

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

        //php artisan snapshot:load my-first-dump
        $this->call('snapshot:load', ['name' => 'mysql-...']);

        return 0;
    }
}
