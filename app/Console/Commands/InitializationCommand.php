<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class InitializationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seymour:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare this application to run for the first time.';

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
        if (! Storage::exists('seymour.sqlite')) {
            touch(storage_path('app/seymour.sqlite'));
            Artisan::call('migrate');
        }

        if (! Storage::exists('public')) {
            Storage::makeDirectory('public');
        }

        if (!Storage::exists('logs')) {
            Storage::makeDirectory('logs');
        }

        return 0;
    }
}
