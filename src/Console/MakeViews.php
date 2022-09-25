<?php

namespace Mrdebug\Crudgen\Console;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:views {directory} {columns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make views';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $template_views_directory=config('crudgen.views_style_directory');
        $separate_style_according_to_actions=config('crudgen.separate_style_according_to_actions');

        if(!File::isDirectory(resource_path('crudgen'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$template_views_directory)))
        {
            if($template_views_directory=='default-theme')
                $this->error("Publish the default theme with: php artisan vendor:publish or create your own default-theme directory here: ".resource_path('crudgen'.DIRECTORY_SEPARATOR.'views'));
            else
                $this->error("Do you have created a directory called ".$template_views_directory." here: ".resource_path('crudgen'.DIRECTORY_SEPARATOR.'views').'?');
            return;
        }
        else
        {
            $stubs=['index', 'create', 'edit', 'show'];
            // check if all files exist
            foreach ($stubs as $key => $stub)
            {
                if (!File::exists($this->getStubPath($template_views_directory).DIRECTORY_SEPARATOR.$stub.'.stub'))
                {
                    $this->error('Please create this file: '.$this->getStubPath($template_views_directory).DIRECTORY_SEPARATOR.$stub.'.stub');
                    return;
                }
            }
        }

        // we create our variables to respect the naming conventions
        $directory_name    = ucfirst($this->argument('directory'));
        $singular_low_name = Str::singular(strtolower($directory_name));
        $plural_low_name   = Str::plural(strtolower($directory_name));


        $columns = $this->argument('columns');
        // if the columns argument is empty, we create an empty array else we explode on the comma
        $columns = ($columns=='') ? [] : explode(',', $columns);

        $th_index=$index_view=$form_create='';

        // we create our placeholders regarding columns
        foreach ($columns as $column)
        {
            $type      = explode(':', trim($column));
            $sql_type  = (count($type)==2) ? $type[1] : 'string';
            $column    = $type[0];
            $type_html = $this->getHtmlType($sql_type);

            // our placeholders
            $th_index    .=str_repeat("\t", 4)."<th>".trim($column)."</th>\n";
            $index_view  .=str_repeat("\t", 5)."<td>{{ DummyCreateVariableSing$->".trim($column)." }}</td>\n";
            $form_create .=str_repeat("\t", 2).'<div class="form-group">'."\n";
            $form_create .=str_repeat("\t", 3).'{{ Form::label(\''.trim($column).'\', \''.ucfirst(trim($column)).'\') }}'."\n";
            $form_create .=str_repeat("\t", 3).'{{ Form::'.$type_html.'(\''.trim($column).'\', null, array(\'class\' => \'form-control\')) }}'."\n";
            $form_create .=str_repeat("\t", 2).'</div>'."\n";
        }


        /* *************************************************************************

                                        VIEWS

        ************************************************************************* */


        // if the directory doesn't exist we create it
        if (!File::isDirectory($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views').DIRECTORY_SEPARATOR.$plural_low_name))
        {
            File::makeDirectory($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views').DIRECTORY_SEPARATOR.$plural_low_name, 0755, true);
            $this->line("<info>Created views directory:</info> $plural_low_name");
        }
        else
            $this->error('Views directory '.$plural_low_name.' already exists');


        /* ************************** index view *************************** */

        $index_stub = File::get($this->getStubPath($template_views_directory).DIRECTORY_SEPARATOR.'index.stub');
        $index_stub = str_replace('DummyCreateVariable$', '$'.$plural_low_name, $index_stub);
        $index_stub = str_replace('DummyCreateVariableSing$', '$'.$singular_low_name, $index_stub);
        $index_stub = str_replace('DummyHeaderTable', $th_index, $index_stub);
        $index_stub = str_replace('DummyIndexTable', $index_view, $index_stub);
        $index_stub = str_replace('DummyCreateVariableSing$', '$'.$singular_low_name, $index_stub);
        $index_stub = str_replace('DummyVariable', $plural_low_name, $index_stub);
        $index_stub = str_replace('DummyExtends', $separate_style_according_to_actions['index']['extends'], $index_stub);
        $index_stub = str_replace('DummySection', $separate_style_according_to_actions['index']['section'], $index_stub);


        // if the index.blade.php file doesn't exist, we create it
        if(!File::exists($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$plural_low_name).DIRECTORY_SEPARATOR.'index.blade.php'))
        {
            File::put($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$plural_low_name).DIRECTORY_SEPARATOR.'index.blade.php', $index_stub);
            $this->line("<info>Created View:</info> index.blade.php");
        }
        else
            $this->error('View index.blade.php already exists');


        /* ************************** create view *************************** */

        $create_stub = File::get($this->getStubPath($template_views_directory).DIRECTORY_SEPARATOR.'create.stub');
        $create_stub = str_replace('DummyVariable', $plural_low_name, $create_stub);
        $create_stub = str_replace('DummyFormCreate', $form_create, $create_stub);
        $create_stub = str_replace('DummyExtends', $separate_style_according_to_actions['create']['extends'], $create_stub);
        $create_stub = str_replace('DummySection', $separate_style_according_to_actions['create']['section'], $create_stub);

        // if the create.blade.php file doesn't exist, we create it
        if(!File::exists($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$plural_low_name).DIRECTORY_SEPARATOR.'create.blade.php'))
        {
            File::put($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$plural_low_name).DIRECTORY_SEPARATOR.'create.blade.php', $create_stub);
            $this->line("<info>Created View:</info> create.blade.php");
        }
        else
            $this->error('View create.blade.php already exists');

        /* ************************** show view *************************** */

        $show_stub = File::get($this->getStubPath($template_views_directory).DIRECTORY_SEPARATOR.'show.stub');
        $show_stub = str_replace('DummyCreateVariableSing$', '$'.$singular_low_name, $show_stub);
        $show_stub = str_replace('DummyExtends', $separate_style_according_to_actions['show']['extends'], $show_stub);
        $show_stub = str_replace('DummySection', $separate_style_according_to_actions['show']['section'], $show_stub);

        // if the show.blade.php file doesn't exist, we create it
        if(!File::exists($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$plural_low_name).DIRECTORY_SEPARATOR.'show.blade.php'))
        {
            File::put($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$plural_low_name).DIRECTORY_SEPARATOR.'show.blade.php', $show_stub);
            $this->line("<info>Created View:</info> show.blade.php");
        }
        else
            $this->error('View show.blade.php already exists');

        /* ************************** edit view *************************** */

        $edit_stub = File::get($this->getStubPath($template_views_directory).DIRECTORY_SEPARATOR.'edit.stub');
        $edit_stub = str_replace('DummyCreateVariableSing$', '$'.$singular_low_name, $edit_stub);
        $edit_stub = str_replace('DummyVariable', $plural_low_name, $edit_stub);
        $edit_stub = str_replace('DummyFormCreate', $form_create, $edit_stub);
        $edit_stub = str_replace('DummyExtends', $separate_style_according_to_actions['edit']['extends'], $edit_stub);
        $edit_stub = str_replace('DummySection', $separate_style_according_to_actions['edit']['section'], $edit_stub);

        // if the edit.blade.php file doesn't exist, we create it
        if(!File::exists($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$plural_low_name).DIRECTORY_SEPARATOR.'edit.blade.php'))
        {
            File::put($this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$plural_low_name).DIRECTORY_SEPARATOR.'edit.blade.php', $edit_stub);
            $this->line("<info>Created View:</info> edit.blade.php");
        }
        else
            $this->error('View edit.blade.php already exists');
    }



    private function getStubPath($template_views_directory)
    {
        return resource_path('crudgen'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$template_views_directory);
    }

    protected function getRealpathBase($directory)
    {
        return realpath(base_path($directory));
    }

    private function getHtmlType($sql_type)
    {
        $conversion =
        [
            'string'  => 'text',
            'text'    => 'textarea',
            'integer' => 'text'
        ];
        return (isset($conversion[$sql_type]) ? $conversion[$sql_type] : 'string');
    }

}
