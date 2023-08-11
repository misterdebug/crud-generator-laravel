<?php

namespace Tests\Console;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;

class MakeServiceTest extends TestCase
{

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('rm:service', ['service_name'=>"PostService", "--force"=>true]);
    }

    public function test_create_service()
    {
        $serviceExisting = array_map(fn($path) => basename($path), glob(app_path('Services/').'*'));
        $this->assertCount(0, $serviceExisting);

        $this->artisan('make:service', ['service_name'=>"PostService"]);

        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/service/PostService.php')), preg_replace('/\s+/', '', File::get(app_path('Services/PostService.php'))));
    }

    public function test_create_service_custom_path()
    {
        config()->set('crudgen.paths.service.path', app_path('Services2'));
        config()->set('crudgen.paths.service.namespace', "App\Services2");
        $serviceExisting = array_map(fn($path) => basename($path), glob(app_path('Services2/').'*'));
        $this->assertCount(0, $serviceExisting);

        $this->artisan('make:service', ['service_name'=>"PostService"]);

        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/service/OtherPostService.php')), preg_replace('/\s+/', '', File::get(app_path('Services2/PostService.php'))));
    }

    public function test_create_service_without_config_file()
    {
        app()->instance('config', new class extends Repository {
            public function unset($key)
            {
                Arr::forget($this->items, $key);
            }
        });

        config()->unset('crudgen.paths.service.path');
        config()->unset('crudgen.paths.service.namespace');

        $serviceExisting = array_map(fn($path) => basename($path), glob(app_path('Services/').'*'));
        $this->assertCount(0, $serviceExisting);

        $this->artisan('make:service', ['service_name'=>"PostService"]);

        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/service/PostService.php')), preg_replace('/\s+/', '', File::get(app_path('Services/PostService.php'))));
    }

    public function tearDown():void
    {
        $this->artisan('rm:service', ['service_name'=>"PostService", "--force"=>true]);
    }
}
