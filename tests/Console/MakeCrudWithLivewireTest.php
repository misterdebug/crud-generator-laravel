<?php

namespace Tests\Console;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

class MakeCrudWithLivewireTest extends TestCase
{

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('rm:crud', ['crud_name'=>"post", "--force"=>true]);
        foreach(glob(database_path('migrations/').date('Y').'*') as $migration)
            File::delete($migration);
    }

    public function test_simple_crud()
    {
        $arrayViewsExisting = array_map(fn($path) => basename($path), glob(resource_path('views/posts/').'*'));
        $this->assertCount(0, $arrayViewsExisting);
        $this->artisan('make:crud', ['crud_name'=>"post", "columns"=>"title, url", "--with-livewire"=>true])
            ->expectsQuestion('Please specify which column you would like to use in the Livewire search?', 'title')
            ->expectsConfirmation('Do you want to create relationships between this model and another one?', 'no');

        //views
        $arrayViewsCreated = array_map(fn($path) => basename($path), glob(resource_path('views/posts/').'*'));
        $this->assertCount(5, $arrayViewsCreated);
        foreach ($arrayViewsCreated as $key => $viewCreated)
        {
            $viewTest = $viewCreated;
            if($viewCreated =="index.blade.php")
            {
                $viewTest = 'index-livewire.blade.php';
            }

            $resultOk= preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/'.$viewTest));
            $viewContent = preg_replace('/\s+/', '', File::get(resource_path('views/posts/'.$viewCreated)));
            $this->assertSame($resultOk, $viewContent , $viewCreated);
        }

        //controller
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/PostsControllerLivewire.php')), preg_replace('/\s+/', '', File::get(app_path('Http/Controllers/PostsController.php'))));

        // request
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/PostRequest.php')), preg_replace('/\s+/', '', File::get(app_path('Http/Requests/PostRequest.php'))));

        //model
        $this->assertContains('Post.php', array_map(fn($path) => basename($path), glob(app_path('Models/').'*')));

        //migration
        $migrationFilename = array_map(fn($path) => basename($path), glob(database_path('migrations/').date('Y').'*create_posts_table.php'));
        $migrationFilename = $migrationFilename[0];
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/migration_posts.php')), preg_replace('/\s+/', '', File::get(database_path('migrations/').DIRECTORY_SEPARATOR.$migrationFilename)));

        //datatable
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/PostDatatable.php')), preg_replace('/\s+/', '', File::get(app_path('Livewire/PostDatatable.php'))));


        //dd(Artisan::output());
    }

    public function tearDown():void
    {
        $this->artisan('rm:crud', ['crud_name'=>"post", "--force"=>true]);
        foreach(glob(database_path('migrations/').date('Y').'*') as $migration)
            File::delete($migration);
    }
}
