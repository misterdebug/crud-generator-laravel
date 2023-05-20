<?php

namespace Mrdebug\Crudgen\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Mrdebug\Crudgen\Services\MakeGlobalService;
use Mrdebug\Crudgen\Services\RemoveCommentableService;

class RemoveCommentable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rm:commentable {commentable_name} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a commentable operation';

    public RemoveCommentableService $removeCommentableService;
    public MakeGlobalService $makeGlobalService;
    public function __construct(RemoveCommentableService $removeCommentableService,MakeGlobalService $makeGlobalService)
    {
        parent::__construct();
        $this->removeCommentableService = $removeCommentableService;
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
        $commentableName  = ucfirst($this->argument('commentable_name'));
        $namingConvention = $this->makeGlobalService->getCommentableNamingConvention($commentableName);
        $force            = $this->option('force');

        $this->deleteFile($namingConvention, 'controller', $force);
        $this->deleteFile($namingConvention, 'request', $force);
        $this->deleteFile($namingConvention, 'model', $force);
    }

    private function deleteFile($namingConvention, $fileType, $force)
    {
        if(File::exists($this->removeCommentableService->pathsForFiles($namingConvention)[$fileType]))
        {
            if ($force || $this->confirm('Do you want to delete this '.$fileType.' '.$this->removeCommentableService->pathsForFiles($namingConvention)[$fileType].'?'))
            {
                if(File::delete($this->removeCommentableService->pathsForFiles($namingConvention)[$fileType]))
                    $this->line("<info>".ucfirst($fileType)." deleted</info>");
            }
        }
    }
}
