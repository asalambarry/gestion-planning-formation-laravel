<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;

/**
 * Class MakeAll
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/03/2020
 */
class MakeAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make full core for the project';

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
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(11);

        $this->info('Welcome to generated commands core for the project');
        $this->line('');

        $this->line('There are twelve steps for making powerfull core');
        $bar->start();

        $this->line('');
        $this->info('Step 1: Creating the migations files');
        $bar->advance();

        $this->line('');
        $this->info('Step 2: Make sure to have the existence of the user migrations file');
        $bar->advance();

        $this->line('');
        $this->info('Step 3: Run command <<php artisan storage:link>>');
        $bar->advance();

        $this->line('');
        $this->info('Step 4: Run command to publish vendor for markdown');
        $bar->advance();

        $this->line('');
        $this->info('Step 4: Run command to publish vendor for mail');
        $bar->advance();

        $this->line('');
        $this->info('Step 5: Make sure to have user_role in config/custum');
        $bar->advance();

        $this->line('');
        $this->info('Step 6: Make sure to have auth folder in app/Http/Controllers');
        $bar->advance();

        $this->line('');
        $this->info('Step 7: Make sure to have auth folder in resources/views');
        $bar->advance();

		$this->line('');
        $this->info('Step 7: Make sure to have in Library folder: CommonForUsers and CustumFunction files');
        $bar->advance();

		$this->line('');
        $this->info('Step 8: Make sure to have the following folder in views: components, emails, errors, layouts, notifs');
        $bar->advance();

		$this->line('');
        $this->info('Step 9: Make sure to have the following files in views: about, aide, cgu, confidentialite, contact, footer, form_contact and welcome');
        $bar->advance();

		$this->line('');
        $this->info('Step 10: Make sure to have the owners logos in storage/app/public dir');
        $bar->advance();

        $this->line('');
        $this->info('Step 10: Make sure to have the CommonForTest file in tests Admin and Api folders');
        $bar->advance();

        $this->info('Successfull finished');

        $bar->finish();

        return true;
    }
}
