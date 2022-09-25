<?php

namespace Mrdebug\Crudgen\Services;

class PathsAndNamespacesService
{
    public function getStubPath()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'stubs';
    }

    public function getDefaultNamespaceRequest($rootNamespace)
    {
        return $rootNamespace.'Http'.DIRECTORY_SEPARATOR.'Requests';
    }

    public function getRealpathBase($directory)
    {
        return realpath(base_path($directory));
    }

    /** paths controller */

    public function getDefaultNamespaceController($rootNamespace)
    {
        return $rootNamespace.'Http'.DIRECTORY_SEPARATOR.'Controllers';
    }

    public function getRealpathBaseController()
    {
        return $this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers');
    }

    public function getRealpathBaseCustomController($namingConvention)
    {
        return $this->getRealpathBaseController().DIRECTORY_SEPARATOR.$namingConvention['plural_name'].'Controller.php';
    }

    public function getControllerStubPath()
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Controller.stub';
    }

    /** paths request */

    public function getRequestStubPath()
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Request.stub';
    }

    public function getRealpathBaseRequest()
    {
        return $this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Requests');
    }

    public function getRealpathBaseCustomRequest($namingConvention)
    {
        return $this->getRealpathBaseRequest().DIRECTORY_SEPARATOR.$namingConvention['singular_name'].'Request.php';
    }

    /** paths models */

    public function getDefaultNamespaceModel($rootNamespace)
    {
        return $rootNamespace.'Models\\';
    }

    public function getDefaultNamespaceCustomModel($rootNamespace, $singularName)
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
        return $this->getRealpathBaseModel().DIRECTORY_SEPARATOR.$namingConvention['singular_name'].'.php';
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


}
