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

    public function getNamingConvention($crudName)
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
}
