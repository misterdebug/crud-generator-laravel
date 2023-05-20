<?php

namespace Mrdebug\Crudgen\Services;


use Illuminate\Support\Str;

class MakeGlobalService
{
    public PathsAndNamespacesService $pathsAndNamespacesService;
    public function __construct(PathsAndNamespacesService $pathsAndNamespacesService)
    {
        $this->pathsAndNamespacesService = $pathsAndNamespacesService;
    }

    public function getNamingConvention($crudName): array
    {
        return
        [
            'plural_name'       => Str::plural($crudName),
            'singular_name'     => Str::singular($crudName),
            'singular_low_name' => Str::singular(strtolower($crudName)),
            'plural_low_name'   => Str::plural(strtolower($crudName)),
            'table_name'        => Str::plural(Str::snake($crudName))
        ];
    }

    public function parseColumns($columns)
    {
        // if the columns argument is empty, we create an empty array else we explode on the comma
        return ($columns=='') ? [] : explode(',', $columns);
    }

    public function cleanLastLineBreak($string)
    {
        return rtrim($string, "\n");
    }

    public function getCommentableNamingConvention($commentableName): array
    {
        return
        [
            'controller_name'            => Str::plural(Str::studly($commentableName)),
            'model_name'                 => Str::singular(Str::studly($commentableName)),
            'singular_low_variable_name' => Str::singular(Str::camel(Str::lower($commentableName))),
            'plural_low_variable_name'   => Str::plural(Str::camel(Str::lower($commentableName))),
            'table_name'                 => Str::plural(Str::snake($commentableName)),
        ];
    }

    public function getAllViewsFiles()
    {
        return glob(resource_path("views".DIRECTORY_SEPARATOR."*".DIRECTORY_SEPARATOR."*.blade.php"));
    }

    public function getCommentableParentModelConvention($commentableParentName): array
    {
        return
        [
            'singular_low_variable_name' => Str::singular(Str::camel(Str::lower($commentableParentName))),
            'plural_low_variable_name'   => Str::plural(Str::camel(Str::lower($commentableParentName))),
        ];
    }
}
