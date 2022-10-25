<?php

namespace Tests\Console;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

class MakeApiCrudTest extends TestCase
{

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('rm:rest-api', ['crud_name'=>"post", "--force"=>true]);
        foreach(glob(database_path('migrations/').date('Y').'*') as $migration)
            File::delete($migration);
    }

    public function test_simple_crud_api()
    {
        $arrayViewsExisting = array_map(fn($path) => basename($path), glob(resource_path('views/posts/').'*'));
        $this->assertCount(0, $arrayViewsExisting);
        $this->artisan('make:rest-api', ['crud_name'=>"post", "columns"=>"title, content:text"])
            ->expectsConfirmation('Do you want to create relationships between this model and an other one?', 'no');

        //views
        $arrayViewsExisting = array_map(fn($path) => basename($path), glob(resource_path('views/posts/').'*'));
        $this->assertCount(0, $arrayViewsExisting);

        //controller
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/api/ApiPostsController.php')), preg_replace('/\s+/', '', File::get(app_path('Http/Controllers/API/PostsController.php'))));

        // request
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/api/PostRequest.php')), preg_replace('/\s+/', '', File::get(app_path('Http/Requests/PostRequest.php'))));

        // resource
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/api/PostResource.php')), preg_replace('/\s+/', '', File::get(app_path('Http/Resources/PostResource.php'))));

        //model
        $this->assertContains('Post.php', array_map(fn($path) => basename($path), glob(app_path('Models/').'*')));

        //migration
        $migrationFilename = array_map(fn($path) => basename($path), glob(database_path('migrations/').date('Y').'*create_posts_table.php'));
        $migrationFilename = $migrationFilename[0];
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/api/migration_posts_api.php')), preg_replace('/\s+/', '', File::get(database_path('migrations/').DIRECTORY_SEPARATOR.$migrationFilename)));

        //dd(Artisan::output());
    }

    public function tearDown():void
    {
        $this->artisan('rm:rest-api', ['crud_name'=>"post", "--force"=>true]);
        foreach(glob(database_path('migrations/').date('Y').'*') as $migration)
            File::delete($migration);
    }
}
