<?php

namespace Mrdebug\Crudgen\Services;

use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Contracts\Foundation\Application;

class RemoveCrudService
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

    public function pathsForFiles($namingConvention)
    {
        return
        [
            'controller' => $this->pathsAndNamespacesService->getRealpathBaseCustomController($namingConvention),
            'request' => $this->pathsAndNamespacesService->getRealpathBaseCustomRequest($namingConvention),
            'model' => $this->pathsAndNamespacesService->getRealpathBaseCustomModel($namingConvention),
            'views' => $this->pathsAndNamespacesService->getRealpathBaseCustomViews($namingConvention),
        ];
    }


}
