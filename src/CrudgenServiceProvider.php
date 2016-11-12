<?php

namespace Mrdebug\Crudgen;

use Illuminate\Support\ServiceProvider;

class CrudgenServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //publish config file
        $this->publishes([__DIR__.'/../config/crudgen.php' => config_path('crudgen.php')]);
        
        //and default-theme
        $this->publishes([__DIR__.'/stubs/default-theme/' => resource_path('crudgen/views/default-theme/')]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/crudgen.php', 'crudgen');

        $this->commands(
            'Mrdebug\Crudgen\Console\MakeCrud',
            'Mrdebug\Crudgen\Console\MakeViews',
            'Mrdebug\Crudgen\Console\RemoveCrud'
        );
    }
}
