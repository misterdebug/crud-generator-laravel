<?php

namespace Mrdebug\Crudgen\Services\Api;

use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Support\Facades\File;
use Mrdebug\Crudgen\Services\MakeGlobalService;
use Mrdebug\Crudgen\Services\PathsAndNamespacesService;
use Symfony\Component\Console\Output\ConsoleOutput;

class MakeResourceService
{
    use InteractsWithIO;

    public PathsAndNamespacesService $pathsAndNamespacesService;
    public MakeGlobalService $makeGlobalService;
    public function __construct(
        PathsAndNamespacesService $pathsAndNamespacesService,
        ConsoleOutput $consoleOutput,
        MakeGlobalService $makeGlobalService
    )
    {
        $this->pathsAndNamespacesService = $pathsAndNamespacesService;
        $this->output = $consoleOutput;
        $this->makeGlobalService = $makeGlobalService;
    }


    public function replaceContentResourceStub($namingConvention, $laravelNamespace)
    {
        $resourceStub = File::get($this->pathsAndNamespacesService->getResourceStubPath());
        $resourceStub = str_replace('DummyNamespace', $this->pathsAndNamespacesService->getDefaultNamespaceResource($laravelNamespace), $resourceStub);
        $resourceStub = str_replace('DummyRootNamespace', $laravelNamespace, $resourceStub);
        $resourceStub = str_replace('DummyClass', $namingConvention['singular_name'].'Resource', $resourceStub);
        return $resourceStub;
    }

    public function findAndReplaceResourcePlaceholderColumns($columns, $resourceStub)
    {
        $resourceArrayContent='';

        // we create our placeholders regarding columns
        foreach ($columns as $column)
        {
            $type     = explode(':', trim($column));
            $column   = $type[0];

            // our placeholders
            $resourceArrayContent .= str_repeat("\t", 3)."'".trim($column)."' => ".'$this->'.trim($column).",\n";
        }

        $resourceArrayContent = $this->makeGlobalService->cleanLastLineBreak($resourceArrayContent);

        // we replace our placeholders
        $resourceStub = str_replace('DummyResource', $resourceArrayContent, $resourceStub);

        return $resourceStub;
    }

    public function createResourceFile($resourceStub, $namingConvention)
    {
        if(!File::exists($this->pathsAndNamespacesService->getRealpathBaseResource()))
            File::makeDirectory($this->pathsAndNamespacesService->getRealpathBaseResource());

        // if the Resource file doesn't exist, we create it
        if(!File::exists($this->pathsAndNamespacesService->getRealpathBaseCustomResource($namingConvention)))
        {
            File::put($this->pathsAndNamespacesService->getRealpathBaseCustomResource($namingConvention), $resourceStub);
            $this->line("<info>Created Resource:</info> ".$namingConvention['singular_name']);
        }
        else
            $this->error('Resource ' .$namingConvention['singular_name']. ' already exists');
    }

    public function makeCompleteResourceFile($namingConvention, $columns, $laravelNamespace)
    {
        $resourceStub = $this->replaceContentResourceStub($namingConvention, $laravelNamespace);
        $resourceStub = $this->findAndReplaceResourcePlaceholderColumns($columns, $resourceStub);

        $this->createResourceFile($resourceStub, $namingConvention);
    }
}
