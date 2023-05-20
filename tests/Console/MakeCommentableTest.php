<?php

namespace Tests\Console;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

class MakeCommentableTest extends TestCase
{

    public function setUp():void
    {
        parent::setUp();
        $this->artisan('rm:crud', ['crud_name'=>"post", "--force"=>true]);
        $this->artisan('rm:commentable', ['commentable_name'=>"comment", "--force"=>true]);
        foreach(glob(database_path('migrations/').date('Y').'*') as $migration)
            File::delete($migration);
    }

    public function test_add_commentable_to_post()
    {
        $arrayViewsExisting = array_map(fn($path) => basename($path), glob(resource_path('views/posts/').'*'));
        $this->assertCount(0, $arrayViewsExisting);
        $this->artisan('make:crud', ['crud_name'=>"post", "columns"=>"title, url"])
            ->expectsConfirmation('Do you want to create relationships between this model and an other one?', 'no');

        $expectedViews =  glob(resource_path("views".DIRECTORY_SEPARATOR."*".DIRECTORY_SEPARATOR."*.blade.php"));
        $this->artisan('make:commentable', ['commentable_name'=>"comment"])
            ->expectsQuestion('What is the name of the other model where you want to add commentable part? ex:Post', 'post')
            ->expectsConfirmation('Do you confirm the creation of this relationship? "$this->belongsTo(\'App\Models\Post\')"', 'yes')
            ->expectsChoice('On which view do you want to add the comment part?', $expectedViews[3], $expectedViews);

        //controller
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/commentable/CommentsController.php')), preg_replace('/\s+/', '', File::get(app_path('Http/Controllers/CommentsController.php'))));

        // request
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/commentable/CommentRequest.php')), preg_replace('/\s+/', '', File::get(app_path('Http/Requests/CommentRequest.php'))));

        //model
        $this->assertContains('Comment.php', array_map(fn($path) => basename($path), glob(app_path('Models/').'*')));
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/commentable/Comment.php')), preg_replace('/\s+/', '', File::get(app_path('models/Comment.php'))));

        //migration
        $migrationFilename = array_map(fn($path) => basename($path), glob(database_path('migrations/').date('Y').'*create_comments_table.php'));
        $migrationFilename = $migrationFilename[0];
        $this->assertSame(preg_replace('/\s+/', '', File::get(__DIR__.DIRECTORY_SEPARATOR.'resultsOk/commentable/migration_comments.php')), preg_replace('/\s+/', '', File::get(database_path('migrations/').DIRECTORY_SEPARATOR.$migrationFilename)));

        //dd(Artisan::output());
    }

    public function tearDown():void
    {
        $this->artisan('rm:crud', ['crud_name'=>"post", "--force"=>true]);
        $this->artisan('rm:commentable', ['commentable_name'=>"comment", "--force"=>true]);
        foreach(glob(database_path('migrations/').date('Y').'*') as $migration)
            File::delete($migration);
    }
}
