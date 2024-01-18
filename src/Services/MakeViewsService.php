<?php

namespace Mrdebug\Crudgen\Services;


use Illuminate\Support\Facades\File;
use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Contracts\Foundation\Application;

class MakeViewsService
{
    use InteractsWithIO;

    public PathsAndNamespacesService $pathsAndNamespacesService;
    public function __construct(
        PathsAndNamespacesService $pathsAndNamespacesService,
        ConsoleOutput $consoleOutput,
        Application $application
    )
    {
        $this->pathsAndNamespacesService = $pathsAndNamespacesService;
        $this->output = $consoleOutput;
        $this->laravel = $application->getNamespace();
    }

    public function createDirectoryViews($namingConvention)
    {
        $directoryName = $this->pathsAndNamespacesService->getRealpathBaseCustomViews($namingConvention);
        // if the directory doesn't exist we create it
        if (!File::isDirectory($directoryName))
        {
            File::makeDirectory($directoryName, 0755, true);
            $this->line("<info>Created views directory:</info> ".$namingConvention['plural_low_name']);
        }
        else
            $this->error('Views directory '.$namingConvention['plural_low_name'].' already exists');
    }

    public function replaceContentControllerStub($namingConvention, $laravelNamespace)
    {
        $controllerStub = File::get($this->pathsAndNamespacesService->getControllerStubPath());
        $controllerStub = str_replace('DummyClass', $namingConvention['plural_name'].'Controller', $controllerStub);
        $controllerStub = str_replace('DummyModel', $namingConvention['singular_name'], $controllerStub);
        $controllerStub = str_replace('DummyVariableSing', $namingConvention['singular_low_name'], $controllerStub);
        $controllerStub = str_replace('DummyVariable', $namingConvention['plural_low_name'], $controllerStub);
        $controllerStub = str_replace('DummyNamespace', $this->pathsAndNamespacesService->getDefaultNamespaceController($laravelNamespace), $controllerStub);
        $controllerStub = str_replace('DummyRootNamespace', $laravelNamespace, $controllerStub);
        return $controllerStub;
    }

    public function findAndReplaceControllerPlaceholderColumns($columns, $controllerStub, $namingConvention)
    {
        $cols='';
        foreach ($columns as $column)
        {
            $type     = explode(':', trim($column));
            $column   = $type[0];

            // our placeholders
            $cols .= str_repeat("\t", 2).'DummyCreateVariableSing$->'.trim($column).'=$request->input(\''.trim($column).'\');'."\n";
        }

        // we replace our placeholders
        $controllerStub = str_replace('DummyUpdate', $cols, $controllerStub);
        $controllerStub = str_replace('DummyCreateVariable$', '$'.$namingConvention['plural_low_name'], $controllerStub);
        $controllerStub = str_replace('DummyCreateVariableSing$', '$'.$namingConvention['singular_low_name'], $controllerStub);

        return $controllerStub;
    }

    public function findAndReplaceIndexViewPlaceholderColumns($columns, $templateViewsDirectory, $namingConvention, $separateStyleAccordingToActions, $withLivewire, $columnInSearch)
    {
        $thIndex=$indexView='';
        foreach ($columns as $column)
        {
            $type      = explode(':', trim($column));
            $column    = $type[0];

            // our placeholders
            $thIndex    .=str_repeat("\t", 4)."<th scope='col'>".trim($column)."</th>\n";

            if($column == $columnInSearch)
                $indexView  .=str_repeat("\t", 5).'<td>{!! $this->search ? $this->highlightTitle(DummyCreateVariableSing$->'.$columnInSearch.') : DummyCreateVariableSing$->'.$columnInSearch.' !!}</td>'."\n";
            else
                $indexView  .=str_repeat("\t", 5)."<td>{{ DummyCreateVariableSing$->".trim($column)." }}</td>\n";
        }

        $indexStub =  $withLivewire
                    ? File::get($this->pathsAndNamespacesService->getCrudgenViewsStubCustom($templateViewsDirectory).DIRECTORY_SEPARATOR.'livewire'.DIRECTORY_SEPARATOR.'index-datatable.stub')
                    : File::get($this->pathsAndNamespacesService->getCrudgenViewsStubCustom($templateViewsDirectory).DIRECTORY_SEPARATOR.'index.stub');

        $indexStub = str_replace('DummyCreateVariable$', '$'.$namingConvention['plural_low_name'], $indexStub);
        $indexStub = str_replace('DummyCreateVariableSing$', '$'.$namingConvention['singular_low_name'], $indexStub);
        $indexStub = str_replace('DummyHeaderTable', $thIndex, $indexStub);
        $indexStub = str_replace('DummyIndexTable', $indexView, $indexStub);
        $indexStub = str_replace('DummyCreateVariableSing$', '$'.$namingConvention['singular_low_name'], $indexStub);
        $indexStub = str_replace('DummyVariable', $namingConvention['plural_low_name'], $indexStub);
        $indexStub = str_replace('DummyExtends', $separateStyleAccordingToActions['index']['extends'], $indexStub);
        $indexStub = str_replace('DummySection', $separateStyleAccordingToActions['index']['section'], $indexStub);

        return $indexStub;
    }

    public function findAndReplaceIndexViewPlaceholderLivewire($templateViewsDirectory, $namingConvention, $separateStyleAccordingToActions, $withLivewire)
    {
        if($withLivewire)
        {
            $indexStub = File::get($this->pathsAndNamespacesService->getCrudgenViewsStubCustom($templateViewsDirectory).DIRECTORY_SEPARATOR.'index-livewire.stub');
            $indexStub = str_replace('{{nameSingLower}}', $namingConvention['singular_low_name'], $indexStub);
            $indexStub = str_replace('DummyExtends', $separateStyleAccordingToActions['index']['extends'], $indexStub);
            $indexStub = str_replace('DummySection', $separateStyleAccordingToActions['index']['section'], $indexStub);
            return $indexStub;
        }
    }

    public function findAndReplaceCreateViewPlaceholderColumns($columns, $templateViewsDirectory, $namingConvention, $separateStyleAccordingToActions)
    {
        $formCreate='';
        foreach ($columns as $column)
        {
            $type      = explode(':', trim($column));
            $sql_type  = (count($type)==2) ? $type[1] : 'string';
            $column    = $type[0];
            $typeHtml = $this->getHtmlType($sql_type);
            $number_types = ['decimal', 'float', 'double'];

            // our placeholders
            $formCreate .=str_repeat("\t", 2).'<div class="mb-3">'."\n";
            $formCreate .=str_repeat("\t", 3).'{{ html()->label(\''.ucfirst(trim($column)).'\', \''.trim($column).'\')->class(\'form-label\') }}'."\n";

            $formCreate .=str_repeat("\t", 3).'{{ html()->'.$typeHtml.'(name: \''.trim($column).'\'';
            if(in_array($sql_type, $number_types))
                $formCreate .=", step: .000001";
            $formCreate .=")->class('form-control') }}\n";
            $formCreate .=str_repeat("\t", 2).'</div>'."\n";
        }

        $createStub = File::get($this->pathsAndNamespacesService->getCrudgenViewsStubCustom($templateViewsDirectory).DIRECTORY_SEPARATOR.'create.stub');
        $createStub = str_replace('DummyVariable', $namingConvention['plural_low_name'], $createStub);
        $createStub = str_replace('DummyFormCreate', $formCreate, $createStub);
        $createStub = str_replace('DummyExtends', $separateStyleAccordingToActions['create']['extends'], $createStub);
        $createStub = str_replace('DummySection', $separateStyleAccordingToActions['create']['section'], $createStub);
        return $createStub;
    }

    public function findAndReplaceShowViewPlaceholderColumns($templateViewsDirectory, $namingConvention, $separateStyleAccordingToActions)
    {
        $showStub = File::get($this->pathsAndNamespacesService->getCrudgenViewsStubCustom($templateViewsDirectory).DIRECTORY_SEPARATOR.'show.stub');
        $showStub = str_replace('DummyCreateVariableSing$', '$'.$namingConvention['singular_low_name'], $showStub);
        $showStub = str_replace('DummyExtends', $separateStyleAccordingToActions['show']['extends'], $showStub);
        $showStub = str_replace('DummySection', $separateStyleAccordingToActions['show']['section'], $showStub);
        return $showStub;
    }

    public function findAndReplaceEditViewPlaceholderColumns($columns, $templateViewsDirectory, $namingConvention, $separateStyleAccordingToActions)
    {
        $formEdit='';
        foreach ($columns as $column)
        {
            $type      = explode(':', trim($column));
            $sql_type  = (count($type)==2) ? $type[1] : 'string';
            $column    = $type[0];
            $typeHtml = $this->getHtmlType($sql_type);

            // our placeholders
            $formEdit .=str_repeat("\t", 2).'<div class="mb-3">'."\n";
            $formEdit .=str_repeat("\t", 3).'{{ html()->label(\''.ucfirst(trim($column)).'\', \''.trim($column).'\') }}'."\n";
            $formEdit .=str_repeat("\t", 3).'{{ html()->'.$typeHtml.'(\''.trim($column).'\', null }}'."\n";
            $formEdit .=str_repeat("\t", 2).'</div>'."\n";
        }

        $editStub = File::get($this->pathsAndNamespacesService->getCrudgenViewsStubCustom($templateViewsDirectory).DIRECTORY_SEPARATOR.'edit.stub');
        $editStub = str_replace('DummyCreateVariableSing$', '$'.$namingConvention['singular_low_name'], $editStub);
        $editStub = str_replace('DummyVariable', $namingConvention['plural_low_name'], $editStub);
        $editStub = str_replace('DummyFormCreate', $formEdit, $editStub);
        $editStub = str_replace('DummyExtends', $separateStyleAccordingToActions['edit']['extends'], $editStub);
        $editStub = str_replace('DummySection', $separateStyleAccordingToActions['edit']['section'], $editStub);
        return $editStub;
    }

    public function createFileOrError($namingConvention, $contentFile, $fileName, $withLivewire=false)
    {
        $path = $withLivewire
        ? $this->pathsAndNamespacesService->getRealpathBaseCustomLivewireViews($namingConvention).DIRECTORY_SEPARATOR.$fileName
        : $this->pathsAndNamespacesService->getRealpathBaseCustomViews($namingConvention).DIRECTORY_SEPARATOR.$fileName;

        if(!File::exists($path))
        {
            File::put($path, $contentFile);
            $this->line("<info>Created View:</info> ".$fileName);
        }
        else
            $this->error('View '.$fileName.' already exists');
    }

    private function getHtmlType($sql_type)
    {
        $conversion =
        [
            'string'  => 'text',
            'text'    => 'textarea',
            'integer' => 'number',
            'float' => 'number',
            'double' => 'number',
            'decimal' => 'number',
            'bool' => 'checkbox'
        ];
        return (isset($conversion[$sql_type]) ? $conversion[$sql_type] : 'string');
    }
}
