<?php

namespace Mrdebug\Crudgen\Services\Commentable;


use Illuminate\Support\Facades\File;
use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Contracts\Foundation\Application;
use Mrdebug\Crudgen\Services\MakeGlobalService;
use Mrdebug\Crudgen\Services\PathsAndNamespacesService;

class MakeCommentableControllerService
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

    public function replaceContentCommentableControllerStub($namingConvention, $laravelNamespace, $nameParent)
    {
        $controllerStub = File::get($this->pathsAndNamespacesService->getCommentableControllerStubPath());
        $controllerStub = str_replace('DummyClass', $namingConvention['controller_name'].'Controller', $controllerStub);
        $controllerStub = str_replace('DummyModel', $namingConvention['model_name'], $controllerStub);
        $controllerStub = str_replace('DummyNamespace', $this->pathsAndNamespacesService->getDefaultNamespaceController($laravelNamespace), $controllerStub);
        $controllerStub = str_replace('DummyRootNamespace', $laravelNamespace, $controllerStub);
        $controllerStub = str_replace('DummyRelationshipName', $nameParent, $controllerStub);

        return $controllerStub;
    }

    public function findAndReplaceCommentableControllerPlaceholderColumns($controllerStub, $namingConvention)
    {
        // we replace our placeholders
        $controllerStub = str_replace('DummyCreateVariableSing$', '$'.$namingConvention['singular_low_variable_name'], $controllerStub);

        return $controllerStub;
    }

    public function createCommentableControllerFile($pathNewController, $controllerStub, $namingConvention)
    {
        if(!File::exists($pathNewController))
        {
            File::put($pathNewController, $controllerStub);
            $this->line("<info>Created Controller:</info> ".$namingConvention['controller_name']);
        }
        else
            $this->error('Controller '.$namingConvention['controller_name'].' already exists');
    }

    public function makeCompleteCommentableControllerFile($namingConvention, $laravelNamespace, $nameParent)
    {
        $controllerStub = $this->replaceContentCommentableControllerStub($namingConvention, $laravelNamespace, $nameParent);
        $controllerStub = $this->findAndReplaceCommentableControllerPlaceholderColumns($controllerStub, $namingConvention);

        // if our controller doesn't exist we create it
        $pathNewController = $this->pathsAndNamespacesService->getRealpathBaseCustomCommentableController($namingConvention);
        $this->createCommentableControllerFile($pathNewController, $controllerStub, $namingConvention);
    }
}
