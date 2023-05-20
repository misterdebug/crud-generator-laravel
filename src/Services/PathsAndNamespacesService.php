<?php

namespace Mrdebug\Crudgen\Services;

class PathsAndNamespacesService
{
    public function getStubPath(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'stubs';
    }

    public function getDefaultNamespaceRequest($rootNamespace): string
    {
        return $rootNamespace.'Http\Requests';
    }

    public function getDefaultNamespaceResource($rootNamespace): string
    {
        return $rootNamespace.'Http\Resources';
    }

    public function getRealpathBase($directory)
    {
        return realpath(base_path($directory));
    }

    /** paths controller */

    public function getDefaultNamespaceController($rootNamespace): string
    {
        return $rootNamespace.'Http\Controllers';
    }

    public function getRealpathBaseController()
    {
        return $this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers');
    }

    public function getRealpathBaseCustomController($namingConvention): string
    {
        return $this->getRealpathBaseController().DIRECTORY_SEPARATOR.$namingConvention['plural_name'].'Controller.php';
    }

    public function getRealpathBaseCustomCommentableController($namingConvention): string
    {
        return $this->getRealpathBaseController().DIRECTORY_SEPARATOR.$namingConvention['controller_name'].'Controller.php';
    }

    public function getControllerStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Controller.stub';
    }

    /** paths api controller */

    public function getDefaultNamespaceApiController($rootNamespace): string
    {
        return $rootNamespace.'Http\Controllers\API';
    }

    public function getRealpathBaseApiController(): string
    {
        return $this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers').DIRECTORY_SEPARATOR.'API';
    }

    public function getRealpathBaseCustomApiController($namingConvention): string
    {
        return $this->getRealpathBaseApiController().DIRECTORY_SEPARATOR.$namingConvention['plural_name'].'Controller.php';
    }

    public function getApiControllerStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'Controller-api.stub';
    }

    public function getCommentableControllerStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'commentable'.DIRECTORY_SEPARATOR.'ControllerCommentable.stub';
    }

    /** paths request */

    public function getRequestStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Request.stub';
    }

    public function getApiRequestStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'request.stub';
    }

    public function getCommentableRequestStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'commentable'.DIRECTORY_SEPARATOR.'Request.stub';
    }

    public function getRealpathBaseRequest(): string
    {
        return $this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http').DIRECTORY_SEPARATOR.'Requests';
    }

    public function getRealpathBaseCustomRequest($namingConvention): string
    {
        return $this->getRealpathBaseRequest().DIRECTORY_SEPARATOR.$namingConvention['singular_name'].'Request.php';
    }

    public function getRealpathBaseCustomCommentableRequest($namingConvention): string
    {
        return $this->getRealpathBaseRequest().DIRECTORY_SEPARATOR.$namingConvention['model_name'].'Request.php';
    }

    /** paths resource */

    public function getResourceStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'resource.stub';
    }

    public function getRealpathBaseResource(): string
    {
        return $this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http').DIRECTORY_SEPARATOR.'Resources';
    }

    public function getRealpathBaseCustomResource($namingConvention): string
    {
        return $this->getRealpathBaseResource().DIRECTORY_SEPARATOR.$namingConvention['singular_name'].'Resource.php';
    }

    /** paths models */

    public function getDefaultNamespaceModel($rootNamespace): string
    {
        return $rootNamespace.'Models\\';
    }

    public function getDefaultNamespaceCustomModel($rootNamespace, $singularName): string
    {
        return $this->getDefaultNamespaceModel($rootNamespace).$singularName;
    }

    public function getRealpathBaseModel()
    {
        return $this->getRealpathBase('app').DIRECTORY_SEPARATOR.'Models';
    }

    public function getModelStubPath()
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Model.stub';
    }

    public function getRealpathBaseCustomModel($namingConvention)
    {
        return isset($namingConvention['singular_name'])
            ? $this->getRealpathBaseModel().DIRECTORY_SEPARATOR.$namingConvention['singular_name'].'.php'
            : $this->getRealpathBaseModel().DIRECTORY_SEPARATOR.$namingConvention['model_name'].'.php';
    }

    /** paths migrations */

    public function getMigrationStubPath()
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'migration.stub';
    }

    public function getRealpathBaseMigration()
    {
        return database_path(DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR);
    }

    /** paths views */
    public function getRealpathBaseViews()
    {
        return $this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views');
    }

    public function getRealpathBaseCustomViews($namingConvention)
    {
        return $this->getRealpathBaseViews().DIRECTORY_SEPARATOR.$namingConvention['plural_low_name'];
    }

    public function getCommentableCommentBlockPath()
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'commentable'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'comment-block.stub';
    }

    public function getCrudgenViewsStub()
    {
        return resource_path('crudgen'.DIRECTORY_SEPARATOR.'views');
    }

    public function getCrudgenViewsStubCustom($templateViewsDirectory)
    {
        return $this->getCrudgenViewsStub().DIRECTORY_SEPARATOR.$templateViewsDirectory;
    }

}
