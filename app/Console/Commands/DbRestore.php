<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DbRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore';

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
        $this->call('snapshot:load', ['name' => 'dump']);

        return 0;
    }
}
