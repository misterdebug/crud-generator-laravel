<?php

namespace Mrdebug\Crudgen\Console;

use Illuminate\Console\Command;
use File;

class RemoveCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:crud {crud_name} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a crud operation';

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
        // we create our variables to respect the naming conventions
        $crud_name         = ucfirst($this->argument('crud_name'));
        $plural_name       = str_plural($crud_name);
        $singular_name     = str_singular($crud_name);
        $singular_low_name = str_singular(strtolower($crud_name));
        $plural_low_name   = str_plural(strtolower($crud_name));

        // if --force option is used, we delete all files without checks
        if($this->option('force'))
        {
            if(File::exists($this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers').DIRECTORY_SEPARATOR.$plural_name.'Controller.php'))
            {
                if(File::delete($this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers').DIRECTORY_SEPARATOR.$plural_name.'Controller.php'))
                    $this->line("<info>Controller deleted</info>"); 
            }

            if(File::isDirectory($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views').DIRECTORY_SEPARATOR.$plural_low_name))
            {
                if(File::deleteDirectory($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views').DIRECTORY_SEPARATOR.$plural_low_name))
                    $this->line("<info>Views deleted</info>"); 
            }

            if(File::exists($this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Requests').DIRECTORY_SEPARATOR.$singular_name.'Request.php'))
            {
                if(File::delete($this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Requests').DIRECTORY_SEPARATOR.$singular_name.'Request.php'))
                    $this->line("<info>Request deleted</info>");
            }

            if(File::exists($this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Models').DIRECTORY_SEPARATOR.$singular_name.'.php'))
            {
                if(File::delete($this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Models').DIRECTORY_SEPARATOR.$singular_name.'.php'))
                    $this->line("<info>Model deleted</info>");
            }
        }
        // else we ask before deleting
        else
        {
            if(File::exists($this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers').DIRECTORY_SEPARATOR.$plural_name.'Controller.php'))
            {
                if ($this->confirm('Do you want to delete this controller '.$this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers').DIRECTORY_SEPARATOR.$plural_name.'Controller.php ?'))
                {
                    if(File::delete($this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers').DIRECTORY_SEPARATOR.$plural_name.'Controller.php'))
                        $this->line("<info>Controller deleted</info>");
                } 
            }

            if(File::isDirectory($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views').DIRECTORY_SEPARATOR.$plural_low_name))
            {
                if ($this->confirm('Do you want delete all files in this views directory '.$this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views').DIRECTORY_SEPARATOR.$plural_low_name.' ? '."\n".implode(", \n",File::files($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views').DIRECTORY_SEPARATOR.$plural_low_name))))
                {
                    if(File::deleteDirectory($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views').DIRECTORY_SEPARATOR.$plural_low_name))
                        $this->line("<info>Views deleted</info>");
                } 
            }

            if(File::exists($this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Requests').DIRECTORY_SEPARATOR.$singular_name.'Request.php'))
            {
                if ($this->confirm('Do you want to delete this request '.$this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Requests').DIRECTORY_SEPARATOR.$singular_name.'Request.php ?'))
                {
                    if(File::delete($this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Requests').DIRECTORY_SEPARATOR.$singular_name.'Request.php'))
                        $this->line("<info>Request deleted</info>");
                } 
            }

            if(File::exists($this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Models').DIRECTORY_SEPARATOR.$singular_name.'.php'))
            {
                if ($this->confirm('Do you want to delete this model '.$this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Models').DIRECTORY_SEPARATOR.$singular_name.'.php ?'))
                {
                    if(File::delete($this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Models').DIRECTORY_SEPARATOR.$singular_name.'.php'))
                        $this->line("<info>Model deleted</info>");
                } 
            }
        }
    }

    protected function getRealpathBase($directory)
    {
        return realpath(base_path($directory));
    }
}
