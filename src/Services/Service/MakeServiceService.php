<?php

namespace Mrdebug\Crudgen\Services\Service;

use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Support\Facades\File;
use Mrdebug\Crudgen\Services\MakeGlobalService;
use Mrdebug\Crudgen\Services\PathsAndNamespacesService;
use Symfony\Component\Console\Output\ConsoleOutput;

class MakeServiceService
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


    public function replaceContentServiceStub($namingConvention, $laravelNamespace)
    {
        $serviceStub = File::get($this->pathsAndNamespacesService->getServiceStubPath());
        $serviceStub = str_replace('DummyNamespace', $this->pathsAndNamespacesService->getDefaultNamespaceService($laravelNamespace), $serviceStub);
        $serviceStub = str_replace('DummyClass', $namingConvention['service_name'], $serviceStub);
        return $serviceStub;
    }

    public function createServiceFile($serviceStub, $namingConvention)
    {
        if(!File::exists($this->pathsAndNamespacesService->getRealpathBaseService()))
            File::makeDirectory($this->pathsAndNamespacesService->getRealpathBaseService());

        $completePathServiceFile = $this->pathsAndNamespacesService->getRealpathBaseCustomService($namingConvention);

        // if the Service file doesn't exist, we create it
        if(!File::exists($completePathServiceFile))
        {
            File::put($completePathServiceFile, $serviceStub);
            $this->line("<info>Created Service:</info> ".$completePathServiceFile);
        }
        else
            $this->error('Service ' .$this->pathsAndNamespacesService->getRealpathBaseCustomService($namingConvention). ' already exists');
    }

    public function makeCompleteServiceFile($namingConvention, $laravelNamespace)
    {
        $serviceStub = $this->replaceContentServiceStub($namingConvention, $laravelNamespace);
        $this->createServiceFile($serviceStub, $namingConvention);
    }
}
