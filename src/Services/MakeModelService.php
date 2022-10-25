<?php

namespace Mrdebug\Crudgen\Services;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;

class MakeModelService
{
    use InteractsWithIO;

    public PathsAndNamespacesService $pathsAndNamespacesService;
    public MakeMigrationService $makeMigrationService;
    public MakeGlobalService $makeGlobalService;
    public function __construct(
        PathsAndNamespacesService $pathsAndNamespacesService,
        MakeMigrationService $makeMigrationService,
        ConsoleOutput $consoleOutput,
        MakeGlobalService $makeGlobalService
    )
    {
        $this->pathsAndNamespacesService = $pathsAndNamespacesService;
        $this->makeMigrationService = $makeMigrationService;
        $this->output = $consoleOutput;
        $this->makeGlobalService = $makeGlobalService;
    }

    public function getAllRelationshipMethodsModel($infos, $singularName, $laravelNamespace)
    {
        $allRelations='';
        foreach ($infos as $info)
        {
            if($info['type']=="hasMany" || $info['type']=="belongsToMany")
                $nameFunction = Str::plural(strtolower($info['name']));
            else
                $nameFunction = Str::singular(strtolower($info['name']));

            $allRelations .= str_repeat("\t", 1).'public function '.$nameFunction.'()'."\n";
            $allRelations .= str_repeat("\t", 1).'{'."\n";
            $allRelations .= str_repeat("\t", 2).'return $this->'.$info['type'].'(\''.$laravelNamespace.'Models\\'.ucfirst(Str::singular($info['name'])).'\');'."\n";
            $allRelations .= str_repeat("\t", 1).'}'."\n\n";


            // in belongsToMany case, we need to create an other table
            if($info['type'] == "belongsToMany")
            {
                $current      = Str::singular(strtolower($singularName));
                $other        = Str::singular(strtolower($info['name']));
                $arrayModels  = [$current, $other];
                sort($arrayModels);
                $name_table   = implode('_', $arrayModels);

                $namingConvention['table_name'] = $name_table;
                $columns = [trim($current)."_id:integer", trim($other)."_id:integer"];
                $this->makeMigrationService->makeCompleteMigrationFile($namingConvention, $columns);
            }
        }

        return $this->makeGlobalService->cleanLastLineBreak($allRelations);
    }

    public function replaceContentModelStub($laravelNamespace, $singularName, $allRelations)
    {
        $modelStub = File::get($this->pathsAndNamespacesService->getModelStubPath());
        $modelStub = str_replace('DummyNamespace', trim($laravelNamespace, '\\'), $modelStub);
        $modelStub = str_replace('DummyClass', $singularName, $modelStub);
        $modelStub = str_replace('DummyRelations', $allRelations, $modelStub);

        return $modelStub;
    }

    public function createModelFile($namingConvention, $modelStub, $singularName)
    {
        if(!File::exists($this->pathsAndNamespacesService->getRealpathBaseCustomModel($namingConvention)))
        {
            File::put($this->pathsAndNamespacesService->getRealpathBaseCustomModel($namingConvention), $modelStub);
            $this->line("<info>Created Model:</info> $singularName");
        }
        else
            $this->error('Model ' .$singularName. ' already exists');
    }

    public function makeCompleteModelFile($infos, $singularName, $namingConvention, $laravelNamespace)
    {
        $allRelations = $this->getAllRelationshipMethodsModel($infos, $singularName, $laravelNamespace);
        $modelStub = $this->replaceContentModelStub($laravelNamespace, $singularName, $allRelations);
        $this->createModelFile($namingConvention, $modelStub, $singularName);
    }

}
