<?php

namespace Mrdebug\Crudgen\Services\Livewire;


use Illuminate\Support\Facades\File;
use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Contracts\Foundation\Application;
use Mrdebug\Crudgen\Services\MakeGlobalService;
use Mrdebug\Crudgen\Services\PathsAndNamespacesService;

class MakeDatatableService
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

    public function replaceContentDatatableStub($namingConvention, $laravelNamespace, $columnInSearch)
    {
        $datatableStub = File::get($this->pathsAndNamespacesService->getDatatableStubPath());
        $datatableStub = str_replace('DummyClass', $namingConvention['singular_name'].'Datatable', $datatableStub);
        $datatableStub = str_replace('DummyModel', $namingConvention['singular_name'], $datatableStub);
        $datatableStub = str_replace('DummyVariable', $namingConvention['plural_low_name'], $datatableStub);
        $datatableStub = str_replace('DummyNamespace', $this->pathsAndNamespacesService->getDefaultNamespaceDatatable($laravelNamespace), $datatableStub);
        $datatableStub = str_replace('DummyRootNamespace', $laravelNamespace, $datatableStub);
        $datatableStub = str_replace('DummyCreateVariable$', '$'.$namingConvention['plural_low_name'], $datatableStub);
        $datatableStub = str_replace('{{name-component}}', $namingConvention['singular_low_name'], $datatableStub);
        $datatableStub = str_replace('{{directory-views}}', $namingConvention['plural_low_name'], $datatableStub);
        $datatableStub = str_replace('{{column-in-search}}', $columnInSearch, $datatableStub);

        return $datatableStub;
    }

    public function createDatatableFile($pathNewDatatable, $datatableStub, $namingConvention)
    {
        if(!File::exists($pathNewDatatable))
        {
            File::put($pathNewDatatable, $datatableStub);
            $this->line("<info>Created Datatable:</info> ".$namingConvention['singular_name']);
        }
        else
            $this->error('Datatable '.$namingConvention['singular_name'].' already exists');
    }

    public function makeCompleteDatatableFile($namingConvention, $laravelNamespace, $columnInSearch)
    {
        $datatableStub = $this->replaceContentDatatableStub($namingConvention, $laravelNamespace, $columnInSearch);

        // if our datatable doesn't exists we create it
        $pathNewDatatable = $this->pathsAndNamespacesService->getRealpathBaseCustomDatatable($namingConvention);
        $this->createDatatableFile($pathNewDatatable, $datatableStub, $namingConvention);
    }
}
