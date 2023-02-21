<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name : Name of the Service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Service class';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = File::get(base_path('stubs/service.stub'));
        $nameArguments = $this->argument('name');
        $splittedName = explode('/', $nameArguments);

        $name = $splittedName[count($splittedName) - 1];

        unset($splittedName[count($splittedName) - 1]);

        $str = Str::of($file)->replaceFirst('{name}', $name);

        $namespace = count($splittedName) > 0 ? '\\' . implode('\\', $splittedName) : '';
        $str = Str::of($str)->replaceFirst('{namespace}', $namespace);

        $path = base_path('app/Services');
        $folders = explode('/', $nameArguments);
        unset($folders[count($folders) - 1]);
        $folder = implode('/', $folders);

        if (!File::exists($path . '/' . $folder)) {
            File::makeDirectory($path . '/' . $folder, 0755, true, true);
        }

        if (File::exists($path . '/' . $nameArguments . '.php')) {
            $this->info('File already exists!');

            return Command::FAILURE;
        }

        File::put($path . '/' . $nameArguments . '.php', $str);
        $this->info('Service was created!');

        return Command::SUCCESS;
    }
}
