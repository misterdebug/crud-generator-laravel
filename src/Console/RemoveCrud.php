<?php

namespace Mrdebug\Crudgen\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Mrdebug\Crudgen\Services\MakeGlobalService;
use Mrdebug\Crudgen\Services\RemoveCrudService;

class RemoveCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rm:crud {crud_name} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a crud operation';

    public RemoveCrudService $removeCrudService;
    public MakeGlobalService $makeGlobalService;
    public function __construct(RemoveCrudService $removeCrudService,MakeGlobalService $makeGlobalService)
    {
        parent::__construct();
        $this->removeCrudService = $removeCrudService;
        $this->makeGlobalService = $makeGlobalService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // we create our variables to respect the naming conventions
        $crudName         = ucfirst($this->argument('crud_name'));
        $namingConvention = $this->makeGlobalService->getNamingConvention($crudName);
        $force            = $this->option('force');

        $this->deleteFile($namingConvention, 'controller', $force);
        $this->deleteDirectory($namingConvention, 'views', $force);
        $this->deleteFile($namingConvention, 'request', $force);
        $this->deleteFile($namingConvention, 'model', $force);
    }

    private function deleteFile($namingConvention, $fileType, $force)
    {
        if(File::exists($this->removeCrudService->pathsForFiles($namingConvention)[$fileType]))
        {
            if ($force || $this->confirm('Do you want to delete this '.$fileType.' '.$this->removeCrudService->pathsForFiles($namingConvention)[$fileType].'?'))
            {
                if(File::delete($this->removeCrudService->pathsForFiles($namingConvention)[$fileType]))
                    $this->line("<info>".ucfirst($fileType)." deleted</info>");
            }
        }
    }

    private function deleteDirectory($namingConvention, $fileType, $force)
    {
        if(File::isDirectory($this->removeCrudService->pathsForFiles($namingConvention)[$fileType]))
        {
            if ($force || $this->confirm('Do you want delete all files in this '.$fileType.' directory '.$this->removeCrudService->pathsForFiles($namingConvention)[$fileType].' ? '."\n".implode(", \n",File::files($this->removeCrudService->pathsForFiles($namingConvention)[$fileType]))))
            {
                if(File::deleteDirectory($this->removeCrudService->pathsForFiles($namingConvention)[$fileType]))
                    $this->line("<info>".ucfirst($fileType)." deleted</info>");
            }
        }
    }
}
