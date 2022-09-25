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

    public function getNamingConvention($crud_name)
    {
        return
        [
            'plural_name'       => Str::plural($crud_name),
            'singular_name'     => Str::singular($crud_name),
            'singular_low_name' => Str::singular(strtolower($crud_name)),
            'plural_low_name'   => Str::plural(strtolower($crud_name)),
            'table_name'        => Str::plural(Str::snake($crud_name))
        ];
    }

    public function parseColumns($columns)
    {
        // if the columns argument is empty, we create an empty array else we explode on the comma
        return ($columns=='') ? [] : explode(',', $columns);
    }
}
