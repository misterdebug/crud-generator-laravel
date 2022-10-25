<?php

namespace Mrdebug\Crudgen\Console;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Mrdebug\Crudgen\Services\Api\MakeApiControllerService;
use Mrdebug\Crudgen\Services\Api\MakeApiRequestService;
use Mrdebug\Crudgen\Services\Api\MakeResourceService;
use Mrdebug\Crudgen\Services\MakeGlobalService;
use Mrdebug\Crudgen\Services\MakeMigrationService;
use Mrdebug\Crudgen\Services\MakeModelService;
use Mrdebug\Crudgen\Services\PathsAndNamespacesService;

class MakeApiCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:rest-api {crud_name} {columns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a REST API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public MakeApiControllerService $makeApiControllerService;
    public MakeApiRequestService $makeApiRequestService;
    public MakeMigrationService $makeMigrationService;
    public MakeModelService $makeModelService;
    public MakeGlobalService $makeGlobalService;
    public PathsAndNamespacesService $pathsAndNamespacesService;
    public MakeResourceService $makeResourceService;

    public function __construct(
        MakeApiControllerService $makeApiControllerService,
        MakeApiRequestService $makeApiRequestService,
        MakeMigrationService $makeMigrationService,
        MakeModelService $makeModelService,
        MakeGlobalService $makeGlobalService,
        PathsAndNamespacesService $pathsAndNamespacesService,
        MakeResourceService $makeResourceService
    )
    {
        parent::__construct();
        $this->makeApiControllerService = $makeApiControllerService;
        $this->makeApiRequestService = $makeApiRequestService;
        $this->makeMigrationService = $makeMigrationService;
        $this->makeModelService = $makeModelService;
        $this->makeGlobalService = $makeGlobalService;
        $this->pathsAndNamespacesService = $pathsAndNamespacesService;
        $this->makeResourceService = $makeResourceService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // we create our variables to respect the naming conventions
        $crudName         = ucfirst($this->argument('crud_name'));
        $namingConvention = $this->makeGlobalService->getNamingConvention($crudName);
        $columns          = $this->makeGlobalService->parseColumns($this->argument('columns'));
        $laravelNamespace = $this->laravel->getNamespace();

        /* *************************************************************************

                                     CONTROLLER

        ************************************************************************* */

        $this->makeApiControllerService->makeCompleteApiControllerFile($namingConvention, $columns, $laravelNamespace);

        /* *************************************************************************

                                        REQUEST

        ************************************************************************* */

        $this->makeApiRequestService->makeCompleteApiRequestFile($namingConvention, $columns, $laravelNamespace);

        /* *************************************************************************

                                        RESOURCE

        ************************************************************************* */

        $this->makeResourceService->makeCompleteResourceFile($namingConvention, $columns, $laravelNamespace);

        /* *************************************************************************

                                        MODEL

        ************************************************************************* */

        if(!File::exists($this->pathsAndNamespacesService->getRealpathBaseModel()))
            File::makeDirectory($this->pathsAndNamespacesService->getRealpathBaseModel());

        // we create our model
        $this->createRelationships([], $namingConvention);


        /* *************************************************************************

                                        MIGRATION

        ************************************************************************* */

        $this->makeMigrationService->makeCompleteMigrationFile($namingConvention, $columns);
    }

    private function createRelationships($infos, $namingConvention)
    {
        $singularName = $namingConvention['singular_name'];
        if ($this->confirm('Do you want to create relationships between this model and an other one?'))
        {
            $type = $this->choice(
                'Which type?',
                ['belongsTo', 'hasOne', 'hasMany', 'belongsToMany', 'Cancel']
            );

            //if cancel choice is selected, we make a basic model
            if($type=="Cancel")
                $this->call('make:model', ['name' => $this->pathsAndNamespacesService->getDefaultNamespaceCustomModel($this->laravel->getNamespace(), $singularName)]);
            //we want a name for this model
            else
                $this->setNameModelRelationship($type, $namingConvention, $infos);
        }
        //we don't confirm, 2 cases
        else
        {
            //$infos is empty we didn't really create a relationship
            if(empty($infos))
                $this->call('make:model', ['name' => $this->pathsAndNamespacesService->getDefaultNamespaceCustomModel($this->laravel->getNamespace(), $singularName)]);

            //we get all relationships asked and we create our model
            else
                $this->makeModelService->makeCompleteModelFile($infos, $singularName, $namingConvention, $this->laravel->getNamespace());
        }
    }

    private function setNameModelRelationship($type, $namingConvention, $infos)
    {
        $nameOtherModel = $this->ask('What is the name of the other model? ex:Post');

        //we stock all relationships in $infos
        $correctNameOtherModel = ucfirst(Str::singular($nameOtherModel));
        $correctNameOtherModelWithNamespace = $this->laravel->getNamespace().'Models\\'.$correctNameOtherModel;
        if($this->confirm('Do you confirm the creation of this relationship? "'.'$this->'.$type.'(\''.$correctNameOtherModelWithNamespace .'\')"'))
        {
            $infos[]= ['name'=>$nameOtherModel, 'type'=>$type];
            $this->createRelationships($infos, $namingConvention);
        }
        else
            $this->setNameModelRelationship($type, $namingConvention, $infos);
    }
}
