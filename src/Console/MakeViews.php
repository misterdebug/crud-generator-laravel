<?php

namespace Mrdebug\Crudgen\Console;

use Illuminate\Console\Command;

use Mrdebug\Crudgen\Services\MakeGlobalService;
use Mrdebug\Crudgen\Services\MakeViewsService;

class MakeViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:views {directory} {columns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make views';

    public MakeViewsService $makeViewsService;
    public MakeGlobalService $makeGlobalService;
    public function __construct(MakeViewsService $makeViewsService, MakeGlobalService $makeGlobalService)
    {
        parent::__construct();
        $this->makeViewsService = $makeViewsService;
        $this->makeGlobalService = $makeGlobalService;
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

        $this->makeViewsService->checkPublishVendorAndViewsDirectoryExists($templateViewsDirectory);

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

        $contentIndex = $this->makeViewsService->findAndReplaceIndexViewPlaceholderColumns($columns, $templateViewsDirectory, $namingConvention, $separateStyleAccordingToActions);
        $this->makeViewsService->createFileOrError($namingConvention, $contentIndex, 'index.blade.php');


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
