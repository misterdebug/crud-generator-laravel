# Crud Generator Laravel 9 and 10 (your time saver)

Crud Generator Laravel is a package that you can integrate in your Laravel to create a REAL CRUD. It includes :

- **Controller** with all the code already written
- **Views** (index, create, edit, show)
- **Model** with relationships
- **Request** file with validation rules
- **Migration** file

And since 1.9.2, a complete **REST API** !

[ NEW ] [Your voice matters! Participate in the polls and vote for future features and improvements](https://github.com/misterdebug/crud-generator-laravel/discussions/categories/polls)


## Installation

1\. Run the following composer command:

``` composer require mrdebug/crudgen --dev ```

2\. If you don't use Laravel Collective Form package in your project, install it:

``` composer require laravelcollective/html ```

<sub>(Note: This step is not required if you don't need views.)</sub>

3\. Publish the configuration file and the default-theme directory for views:

``` php artisan vendor:publish --provider="Mrdebug\Crudgen\CrudgenServiceProvider" ```


## Usage

### Create CRUD (or REST API)

Let's illustrate with a real life example : Building a blog

A `Post` has many (hasMany) `Comment` and belongs to many (belongsToMany) `Tag`

A `Post` can have a `title` and a `content` fields

Let's do this 🙂

<sub>If you need a REST API instead of CRUD, [read this wiki](https://github.com/misterdebug/crud-generator-laravel/wiki/Make-a-complete-REST-API-instead-of-CRUD)</sub>

#### CRUD generator command :

``` php artisan make:crud nameOfYourCrud "column1:type, column2" ``` (theory)

``` php artisan make:crud post "title:string, content:text" ``` (for our example)

<sub>[Available options](https://github.com/misterdebug/crud-generator-laravel/wiki/Available-options-when-you-use-make:crud-command)</sub>

When you call this command, the controller, views and request are generated with your fields (in this case, title and content).
![image](https://user-images.githubusercontent.com/23297600/192172786-1703f7b8-f577-45c1-b0f9-296999827af2.png)

Now let's add our relationships (`Comment` and `Tag` models) :

![image](https://user-images.githubusercontent.com/23297600/192173041-6c71d727-1e29-4edc-9397-bdb07f44a378.png)

We add a `hasMany` relationship between our `Post` and `Comment`
and a `belongsToMany` with `Tag`

Two migrations are created (`create_posts` AND `create_post_tag`).

`create_posts` is your table for your `Post` model

`create_post_tag` is a pivot table to handle the `belongsToMany` relationship

`Post` model is generated too with both relationships added

![image](https://user-images.githubusercontent.com/23297600/192173463-f3e61b41-373a-44a8-870f-fc837968a5c7.png)

### Migration

Both migration files are created in your **database/migrations** directory. If necessary edit them and run :
   
``` php artisan migrate ```

### Controller

A controller file is created in your **app/Http/Controllers** directory. All methods (index, create, store, show, edit, update, destroy) are filled with your fields.

### Routes

To create your routes for this new controller, you can do this :

``` Route::resource('posts', PostsController::class); ``` <sub><sup>(don't forget to import your `PostsController` in your `web.php` file)</sup></sub>

##### Screenshots

`/posts/create` :
![image](https://user-images.githubusercontent.com/23297600/192176702-dc0371f4-5d1b-49e3-a9ea-7352a33187d4.png)


`/posts` :
![image](https://user-images.githubusercontent.com/23297600/192176845-b3722083-90a9-4257-90d1-8a2eb28baa01.png)

You can `edit` and `delete` your new post. And a `show` page is created too 🙂

### Request

A request file is created in your **app/Http/Requests** directory. By default, all fields are required, you can edit it according to your needs.

### Views

A views directory is created in your **resources/views** directory.
<sub>If you want to customize generated views : [https://github.com/misterdebug/crud-generator-laravel/wiki/Custom-your-views](https://github.com/misterdebug/crud-generator-laravel/wiki/Custom-your-views)</sub>

You can create views independently of the CRUD generator with :
``` php artisan make:views nameOfYourDirectoryViews "column1:type, column2" ```

## Finish your blog

Add your `Comment` CRUD (with a column `comment` and a `post_id`)

``` php artisan make:crud comment "comment:text, post_id:integer" ```

Add your `Tag` CRUD (with a `column` name)

``` php artisan make:crud tag "name" ```

FYI : `Comment` is a specific case and you can use `make:commentable` command
<small>[Docs about commentable](https://github.com/misterdebug/crud-generator-laravel/wiki/Add-a-commentable-structure-to-any-model)</small>

Finished 🎉

## Remove a CRUD

You can delete all files (except migrations) created by the `make:crud` command at any time. No need to remove files manually :

``` php artisan rm:crud nameOfYourCrud --force ```

``` php artisan rm:crud post --force ``` (in our example)

The `--force` flag (optional) deletes all files without confirmation

![image](https://user-images.githubusercontent.com/23297600/192183601-a4f8d206-3920-4f8a-8e0d-cf8442894e07.png)


## License

This package is licensed under the [license MIT](http://opensource.org/licenses/MIT).
