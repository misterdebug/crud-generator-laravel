<?php

namespace Mrdebug\Crudgen\Services\Api;


use Illuminate\Support\Facades\File;
use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Contracts\Foundation\Application;
use Mrdebug\Crudgen\Services\MakeGlobalService;
use Mrdebug\Crudgen\Services\PathsAndNamespacesService;

class MakeApiControllerService
{
    use InteractsWithIO;

    public PathsAndNamespacesService $pathsAndNamespacesService;
    public MakeGlobalService $makeGlobalService;
    public function __construct(
        PathsAndNamespacesService $pathsAndNamespacesService,
        ConsoleOutput $consoleOutput,
        Application $application,
        MakeGlobalService $makeGlobalService
    )
    {
        $this->pathsAndNamespacesService = $pathsAndNamespacesService;
        $this->output = $consoleOutput;
        $this->laravel = $application->getNamespace();
        $this->makeGlobalService = $makeGlobalService;
    }

    public function replaceContentApiControllerStub($namingConvention, $laravelNamespace)
    {
        $controllerStub = File::get($this->pathsAndNamespacesService->getApiControllerStubPath());
        $controllerStub = str_replace('DummyClass', $namingConvention['plural_name'].'Controller', $controllerStub);
        $controllerStub = str_replace('DummyResource', $namingConvention['singular_name'].'Resource', $controllerStub);
        $controllerStub = str_replace('DummyModel', $namingConvention['singular_name'], $controllerStub);
        $controllerStub = str_replace('DummyVariableSing', $namingConvention['singular_low_name'], $controllerStub);
        $controllerStub = str_replace('DummyVariable', $namingConvention['plural_low_name'], $controllerStub);
        $controllerStub = str_replace('DummyNamespace', $this->pathsAndNamespacesService->getDefaultNamespaceApiController($laravelNamespace), $controllerStub);
        $controllerStub = str_replace('DummyRootNamespace', $laravelNamespace, $controllerStub);

        return $controllerStub;
    }

    public function findAndReplaceApiControllerPlaceholderColumns($columns, $controllerStub, $namingConvention)
    {
        $cols='';
        foreach ($columns as $column)
        {
            $type     = explode(':', trim($column));
            $column   = $type[0];

            // our placeholders
            $cols .= str_repeat("\t", 2).'DummyCreateVariableSing$->'.trim($column).' = $request->input(\''.trim($column).'\');'."\n";
        }

        $cols = $this->makeGlobalService->cleanLastLineBreak($cols);

        // we replace our placeholders
        $controllerStub = str_replace('DummyUpdate', $cols, $controllerStub);
        $controllerStub = str_replace('DummyCreateVariable$', '$'.$namingConvention['plural_low_name'], $controllerStub);
        $controllerStub = str_replace('DummyCreateVariableSing$', '$'.$namingConvention['singular_low_name'], $controllerStub);

        return $controllerStub;
    }

    public function createApiControllerFile($pathNewController, $controllerStub, $namingConvention)
    {
        if(!File::exists($this->pathsAndNamespacesService->getRealpathBaseApiController()))
            File::makeDirectory($this->pathsAndNamespacesService->getRealpathBaseApiController());

        if(!File::exists($pathNewController))
        {
            File::put($pathNewController, $controllerStub);
            $this->line("<info>Created Controller:</info> ".$namingConvention['plural_name']);
        }
        else
            $this->error('Controller '.$namingConvention['plural_name'].' already exists');
    }

    public function makeCompleteApiControllerFile($namingConvention, $columns, $laravelNamespace)
    {
        $controllerStub = $this->replaceContentApiControllerStub($namingConvention, $laravelNamespace);
        $controllerStub = $this->findAndReplaceApiControllerPlaceholderColumns($columns, $controllerStub, $namingConvention);

        // if our controller doesn't exist we create it
        $pathNewController = $this->pathsAndNamespacesService->getRealpathBaseCustomApiController($namingConvention);
        $this->createApiControllerFile($pathNewController, $controllerStub, $namingConvention);
    }
}
