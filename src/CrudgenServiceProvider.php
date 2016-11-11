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
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            'Mrdebug\Crudgen\Console\MakeCrud',
            'Mrdebug\Crudgen\Console\RemoveCrud'
        );
    }
}
