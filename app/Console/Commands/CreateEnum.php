<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum {name : Name of the enum}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Enum class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $returnType = $this->choice(
            'What is return type?',
            ['string', 'int'],
            0
        );
        $dummy = $returnType == 'string' ? '"default"' : 0;

        $file = File::get(base_path('stubs/enum.stub'));
        $nameArguments = $this->argument('name');
        $splittedName = explode('/', $nameArguments);

        $name = $splittedName[count($splittedName) - 1];

        unset($splittedName[count($splittedName) - 1]);

        $str = Str::of($file)->replaceFirst('{name}', $name);
        $str = Str::of($str)->replaceFirst('{returnType}', $returnType);
        $str = Str::of($str)->replaceFirst('{dummy}', $dummy);

        $namespace = count($splittedName) > 0 ? '\\' . implode('\\', $splittedName) : '';
        $str = Str::of($str)->replaceFirst('{namespace}', $namespace);


        $path = base_path('app/Enums');
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
        $this->info('Enum was created!');

        return Command::SUCCESS;
    }
}
