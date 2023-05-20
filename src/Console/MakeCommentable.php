<?php

namespace Mrdebug\Crudgen\Console;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Mrdebug\Crudgen\Exceptions\ConsoleException;
use Mrdebug\Crudgen\Services\Commentable\EditCommentableView;
use Mrdebug\Crudgen\Services\Commentable\MakeCommentableRequestService;
use Mrdebug\Crudgen\Services\Commentable\MakeCommentableControllerService;
use Mrdebug\Crudgen\Services\MakeGlobalService;
use Mrdebug\Crudgen\Services\MakeMigrationService;
use Mrdebug\Crudgen\Services\MakeModelService;
use Mrdebug\Crudgen\Services\PathsAndNamespacesService;

class MakeCommentable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:commentable {commentable_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add commentable fields to existing view';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public MakeCommentableControllerService $makeCommentableControllerService;
    public MakeCommentableRequestService $makeCommentableRequestService;
    public MakeMigrationService $makeMigrationService;
    public MakeModelService $makeModelService;
    public EditCommentableView $editCommentableView;
    public MakeGlobalService $makeGlobalService;
    public PathsAndNamespacesService $pathsAndNamespacesService;
    public string $nameParentModel = "";
    public string $pathViewCommentable = "";

    public function __construct(
        MakeCommentableControllerService $makeCommentableControllerService,
        MakeCommentableRequestService $makeCommentableRequestService,
        MakeMigrationService $makeMigrationService,
        MakeModelService $makeModelService,
        EditCommentableView $editCommentableView,
        MakeGlobalService $makeGlobalService,
        PathsAndNamespacesService $pathsAndNamespacesService,
    )
    {
        parent::__construct();
        $this->makeCommentableControllerService = $makeCommentableControllerService;
        $this->makeCommentableRequestService = $makeCommentableRequestService;
        $this->makeMigrationService = $makeMigrationService;
        $this->makeModelService = $makeModelService;
        $this->editCommentableView = $editCommentableView;
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
        // we create our variables to respect the naming conventions
        $commentableName  = ucfirst($this->argument('commentable_name'));
        $namingConvention = $this->makeGlobalService->getCommentableNamingConvention($commentableName);
        $laravelNamespace = $this->laravel->getNamespace();


        /* *************************************************************************

                                        REQUEST

        ************************************************************************* */

        $this->makeCommentableRequestService->makeCommentableCompleteRequestFile($namingConvention, $laravelNamespace);

        /* *************************************************************************

                                        MODEL

        ************************************************************************* */

        if(!File::exists($this->pathsAndNamespacesService->getRealpathBaseModel()))
            File::makeDirectory($this->pathsAndNamespacesService->getRealpathBaseModel());

        // we create our model
        $this->setNameModelRelationship($namingConvention);

        /* *************************************************************************

                                     CONTROLLER

        ************************************************************************* */

        $namingConventionParent = $this->makeGlobalService->getCommentableParentModelConvention($this->nameParentModel);
        $this->makeCommentableControllerService->makeCompleteCommentableControllerFile($namingConvention, $laravelNamespace, $namingConventionParent['singular_low_variable_name']);


        /* *************************************************************************

                                        MIGRATION

        ************************************************************************* */

        $columns = ['comment:text'];
        if($this->nameParentModel !== "")
            $columns[]= $this->nameParentModel."_id:integer";
        $this->makeMigrationService->makeCompleteMigrationFile($namingConvention, $columns);

        /* *************************************************************************

                                        VIEW

        ************************************************************************* */
        $this->askChangeView($namingConvention);
    }

    private function setNameModelRelationship($namingConvention)
    {
        $type = "belongsTo";
        $infos = [];
        $singularName = $namingConvention['model_name'];
        $nameOtherModel = $this->ask('What is the name of the other model where you want to add commentable part? ex:Post');

        if($nameOtherModel === null)
            throw new ConsoleException('Please provide a model name');

        $this->nameParentModel = $nameOtherModel;

        $correctNameOtherModel = ucfirst(Str::singular($nameOtherModel));
        $correctNameOtherModelWithNamespace = $this->laravel->getNamespace().'Models\\'.$correctNameOtherModel;
        if($this->confirm('Do you confirm the creation of this relationship? "'.'$this->'.$type.'(\''.$correctNameOtherModelWithNamespace .'\')"'))
        {
            $infos[] = ['name'=>$nameOtherModel, 'type'=>$type];
            $this->makeModelService->makeCompleteModelFile($infos, $singularName, $namingConvention, $this->laravel->getNamespace());
        }
        else
            $this->setNameModelRelationship($namingConvention);
    }

    private function askChangeView($namingConvention)
    {
        $allViews = $this->makeGlobalService->getAllViewsFiles();
        $this->error("Before to continue, please indicate this placeholder : {{comment_here}} where you want the form to be displayed");
        $chosenView = $this->choice(
            'On which view do you want to add the comment part?',
            $allViews,
        );
        $this->editCommentableView->editViewFile($chosenView, $namingConvention, $this->nameParentModel);
    }
}
