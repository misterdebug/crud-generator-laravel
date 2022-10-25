<?php

namespace Tests\Console;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

class MakeCrudTest extends TestCase
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
        $this->artisan('make:crud', ['crud_name'=>"post", "columns"=>"title, url"])
            ->expectsConfirmation('Do you want to create relationships between this model and an other one?', 'no');

        //views
        $arrayViewsCreated = array_map(fn($path) => basename($path), glob(resource_path('views/posts/').'*'));
        $this->assertCount(4, $arrayViewsCreated);
        foreach ($arrayViewsCreated as $key => $viewCreated)
        {
            $resultOk= preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/'.$viewCreated));
            $viewContent = preg_replace('/\s+/', '', File::get(resource_path('views/posts/'.$viewCreated)));
            $this->assertSame($resultOk, $viewContent , $viewCreated);
        }

        //controller
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/PostsController.php')), preg_replace('/\s+/', '', File::get(app_path('Http/Controllers/PostsController.php'))));

        // request
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/PostRequest.php')), preg_replace('/\s+/', '', File::get(app_path('Http/Requests/PostRequest.php'))));

        //model
        $this->assertContains('Post.php', array_map(fn($path) => basename($path), glob(app_path('Models/').'*')));

        //migration
        $migrationFilename = array_map(fn($path) => basename($path), glob(database_path('migrations/').date('Y').'*create_posts_table.php'));
        $migrationFilename = $migrationFilename[0];
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/migration_posts.php')), preg_replace('/\s+/', '', File::get(database_path('migrations/').DIRECTORY_SEPARATOR.$migrationFilename)));

        //dd(Artisan::output());
    }

    //todo
    public function _simple_crud_post_with_tags_and_comments()
    {
        $arrayViewsExisting = array_map(fn($path) => basename($path), glob(resource_path('views/posts/').'*'));
        $this->assertCount(0, $arrayViewsExisting);
        $this->artisan('make:crud', ['crud_name'=>"post", "columns"=>"title, url"])
            ->expectsConfirmation('Do you want to create relationships between this model and an other one?', 'yes')
            ->expectsQuestion('Which type?', 'hasMany')
            ->expectsQuestion('What is the name of the other model? ex:Post', 'Comment')
            ->expectsConfirmation('Do you confirm the creation of this relationship? "$this->hasMany(\'App\Models\Comment\')"', 'yes')

            ;

        //views
        $arrayViewsCreated = array_map(fn($path) => basename($path), glob(resource_path('views/posts/').'*'));
        $this->assertCount(4, $arrayViewsCreated);
        foreach ($arrayViewsCreated as $key => $viewCreated) {
            $this->assertSame(File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/'.$viewCreated), File::get(resource_path('views/posts/'.$viewCreated)), $viewCreated);
        }

        //controller
        $this->assertSame(File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/PostsController.php'), File::get(app_path('Http/Controllers/PostsController.php')));

        // request
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/PostRequest.php')), preg_replace('/\s+/', '', File::get(app_path('Http/Requests/PostRequest.php'))));

        //model
        $this->assertContains('Post.php', array_map(fn($path) => basename($path), glob(app_path('Models/').'*')));

        //migration
        $migrationFilename = array_map(fn($path) => basename($path), glob(database_path('migrations/').date('Y').'*create_posts_table.php'));
        $migrationFilename = $migrationFilename[0];
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/migration_posts.php')), preg_replace('/\s+/', '', File::get(database_path('migrations/').DIRECTORY_SEPARATOR.$migrationFilename)));

        //dd(Artisan::output());
    }

    public function tearDown():void
    {
        $this->artisan('rm:crud', ['crud_name'=>"post", "--force"=>true]);
        foreach(glob(database_path('migrations/').date('Y').'*') as $migration)
            File::delete($migration);
    }
}
