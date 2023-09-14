<?php

namespace Mrdebug\Crudgen\Console;

use Illuminate\Console\Command;

use Mrdebug\Crudgen\Services\MakeGlobalService;
use Mrdebug\Crudgen\Services\MakeViewsService;
use Illuminate\Support\Facades\File;
use Mrdebug\Crudgen\Services\PathsAndNamespacesService;

class MakeViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:views {directory} {columns} {--with-livewire} {searchableColumn?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make views';

    public MakeViewsService $makeViewsService;
    public MakeGlobalService $makeGlobalService;
    public PathsAndNamespacesService $pathsAndNamespacesService;

    public function __construct(
        MakeViewsService $makeViewsService,
        MakeGlobalService $makeGlobalService,
        PathsAndNamespacesService $pathsAndNamespacesService
    )
    {
        parent::__construct();
        $this->makeViewsService = $makeViewsService;
        $this->makeGlobalService = $makeGlobalService;
        $this->pathsAndNamespacesService = $pathsAndNamespacesService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $templateViewsDirectory = config('crudgen.views_style_directory');
        $separateStyleAccordingToActions = config('crudgen.separate_style_according_to_actions');
        $withLivewire = $this->option('with-livewire');
        $columnInSearch = $this->argument('searchableColumn');

        if(!File::isDirectory($this->pathsAndNamespacesService->getCrudgenViewsStubCustom($templateViewsDirectory)))
        {
            if($templateViewsDirectory=='default-theme')
                $this->error("Publish the default theme with: php artisan vendor:publish --provider=\"Mrdebug\Crudgen\CrudgenServiceProvider\" or create your own default-theme directory here: ".$this->pathsAndNamespacesService->getCrudgenViewsStub());
            else
                $this->error("Do you have created a directory called ".$templateViewsDirectory." here: ".$this->pathsAndNamespacesService->getCrudgenViewsStub().'?');
            return;
        }
        else
        {
            $stubs=['index', 'create', 'edit', 'show'];
            // check if all stubs exist
            foreach ($stubs as $stub)
            {
                if (!File::exists($this->pathsAndNamespacesService->getCrudgenViewsStubCustom($templateViewsDirectory).DIRECTORY_SEPARATOR.$stub.'.stub'))
                {
                    $this->error('Please create this file: '.$this->pathsAndNamespacesService->getCrudgenViewsStubCustom($templateViewsDirectory).DIRECTORY_SEPARATOR.$stub.'.stub');
                    return;
                }
            }
        }

        // we create our variables to respect the naming conventions
        $directoryName    = $this->argument('directory');
        $namingConvention = $this->makeGlobalService->getNamingConvention($directoryName);

        $columns = $this->argument('columns');
        // if the columns argument is empty, we create an empty array else we explode on the comma
        $columns = ($columns=='') ? [] : explode(',', $columns);

        /* *************************************************************************

                                        VIEWS

        ************************************************************************* */

        // if the directory doesn't exist we create it
        $this->makeViewsService->createDirectoryViews($namingConvention);


        /* ************************** index view *************************** */

        $contentIndex = $this->makeViewsService->findAndReplaceIndexViewPlaceholderColumns($columns, $templateViewsDirectory, $namingConvention, $separateStyleAccordingToActions, $withLivewire, $columnInSearch);
        $this->makeViewsService->createFileOrError($namingConvention, $contentIndex, $withLivewire ? $namingConvention['singular_low_name'].'-datatable.blade.php' : 'index.blade.php');

        if($withLivewire)
        {
            $contentDatableIndexLivewire = $this->makeViewsService->findAndReplaceIndexViewPlaceholderLivewire($templateViewsDirectory, $namingConvention, $separateStyleAccordingToActions, $withLivewire);
            $this->makeViewsService->createFileOrError($namingConvention, $contentDatableIndexLivewire, 'index.blade.php');
        }


        /* ************************** create view *************************** */

        $contentCreate = $this->makeViewsService->findAndReplaceCreateViewPlaceholderColumns($columns, $templateViewsDirectory, $namingConvention, $separateStyleAccordingToActions);
        $this->makeViewsService->createFileOrError($namingConvention, $contentCreate, 'create.blade.php');

        /* ************************** show view *************************** */

        $contentShow = $this->makeViewsService->findAndReplaceShowViewPlaceholderColumns($templateViewsDirectory, $namingConvention, $separateStyleAccordingToActions);
        $this->makeViewsService->createFileOrError($namingConvention, $contentShow, 'show.blade.php');

        /* ************************** edit view *************************** */

        $contentEdit = $this->makeViewsService->findAndReplaceEditViewPlaceholderColumns($columns, $templateViewsDirectory, $namingConvention, $separateStyleAccordingToActions);
        $this->makeViewsService->createFileOrError($namingConvention, $contentEdit, 'edit.blade.php');
    }
}
