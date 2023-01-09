<?php

namespace Mrdebug\Crudgen\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Mrdebug\Crudgen\Services\Api\RemoveApiCrudService;
use Mrdebug\Crudgen\Services\MakeGlobalService;

class RemoveApiCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rm:rest-api {crud_name} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a REST API operation';

    public RemoveApiCrudService $removeApiCrudService;
    public MakeGlobalService $makeGlobalService;
    public function __construct(RemoveApiCrudService $removeApiCrudService,MakeGlobalService $makeGlobalService)
    {
        parent::__construct();
        $this->removeApiCrudService = $removeApiCrudService;
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
        $this->deleteFile($namingConvention, 'request', $force);
        $this->deleteFile($namingConvention, 'model', $force);
        $this->deleteFile($namingConvention, 'resource', $force);
    }

    private function deleteFile($namingConvention, $fileType, $force)
    {
        if(File::exists($this->removeApiCrudService->pathsForFiles($namingConvention)[$fileType]))
        {
            if ($force || $this->confirm('Do you want to delete this '.$fileType.' '.$this->removeApiCrudService->pathsForFiles($namingConvention)[$fileType].'?'))
            {
                if(File::delete($this->removeApiCrudService->pathsForFiles($namingConvention)[$fileType]))
                    $this->line("<info>".ucfirst($fileType)." deleted</info>");
            }
        }
    }
}
