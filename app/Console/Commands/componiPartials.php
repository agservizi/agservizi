<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class componiPartials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'componi:partials';

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
        $this->rinominaFiles(resource_path('views/metronic'));


        return Command::SUCCESS;
    }


    protected function rinominaFiles($cartella)
    {

        foreach (File::files($cartella) as $file) {

            $this->sostituisciTesto($file->getPathname());

            if ($file->getExtension() == 'html') {
                rename($file->getPathname(), Str::replaceLast('html', 'blade.php', $file->getPathname()));
            }


        }
        foreach (File::directories($cartella) as $dir) {

            $this->rinominaFiles($dir);

        }


    }


    protected function sostituisciTesto($filePath)
    {
        $corpo = File::get($filePath);
        $re = '/\s*(<!--layout-partial:[\s\S]+?-->)/m';

        preg_match_all($re, $corpo, $matches, PREG_SET_ORDER, 0);


        foreach ($matches as $s) {
            $trovato = $s[0];
            $nuovaStringa = Str::of($trovato)
                ->replace('<!--layout-partial:', "@include('metronic.")
                ->replace('/', '.')
                ->replace('-->', "')")->replace('.html', '');
            $corpo = str_replace($trovato, $nuovaStringa, $corpo);
        }

        //Modifico assets in asstes_backend
        $corpo = str_replace('"assets/', '"/assets_backend/', $corpo);

        File::put($filePath, $corpo);

    }
}
