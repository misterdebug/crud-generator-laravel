<?php

namespace Mrdebug\Crudgen\Console;

use Illuminate\Console\Command;

use Mrdebug\Crudgen\Services\MakeGlobalService;
use Mrdebug\Crudgen\Services\PathsAndNamespacesService;
use Mrdebug\Crudgen\Services\Service\MakeServiceService;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {service_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a service file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public MakeGlobalService $makeGlobalService;
    public PathsAndNamespacesService $pathsAndNamespacesService;
    public MakeServiceService $makeServiceService;

    public function __construct(
        MakeGlobalService $makeGlobalService,
        PathsAndNamespacesService $pathsAndNamespacesService,
        MakeServiceService $makeServiceService
    )
    {
        parent::__construct();
        $this->makeGlobalService = $makeGlobalService;
        $this->pathsAndNamespacesService = $pathsAndNamespacesService;
        $this->makeServiceService = $makeServiceService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // we create our variables to respect the naming conventions
        $serviceName  = ucfirst($this->argument('service_name'));
        $namingConvention = $this->makeGlobalService->getCommentableNamingConvention($serviceName);
        $laravelNamespace = $this->laravel->getNamespace();

        $this->makeServiceService->makeCompleteServiceFile($namingConvention, $laravelNamespace);
    }
}
