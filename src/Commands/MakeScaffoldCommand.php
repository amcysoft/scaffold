<?php

namespace Amcysoft\Scaffold\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\Storage;

class MakeScaffoldCommand extends Command
{
    private $path;
    private $Model;
    private $model;
    private $Models;
    private $models;

    private $schema;
    private $field_names;
    private $table_header;
    private $table_data;
    private $details;
    private $seeder_data;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "make:scaffold {entity} {attribute:type*}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating schema, controller, model, and views with scaffold command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->path = __DIR__.'/../';

        $this->Model = '';
        $this->model = '';
        $this->Models = '';
        $this->models = '';

        $this->schema = '';
        $this->field_names = '';
        $this->table_header = '';
        $this->table_data = '';
        $this->details = '';
        $this->seeder_data = '';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->argument('attribute:type');
        $entity = $this->argument('entity');

        $attributes = $this->validateAndSetNames($entity, $arguments);
        
        $this->info("Generating Schema ...");
        $this->generateSchema($attributes);

        $this->info("Generating Controller ...");
        $this->generateController();

        $this->info("Generating Model ...");
        $this->generateModel($attributes);

        $this->info("Generating Views ...");
        $this->generateViews($attributes);

        $this->info("Adding Route ...");
        $route = "\nRoute::resource('".$this->models."', '".$this->Models."Controller');";
        file_put_contents('app/Http/routes.php', $route, FILE_APPEND | LOCK_EX);

        $this->info("Running Migration ...");
        \Artisan::call('migrate');

        $this->info("Running Auto load ...");
        exec('composer dump-autoload');

        $this->info("Seeding Data ...");
        $this->seedData($attributes);

        $this->info("Running Seeds ...");
        \Artisan::call('db:seed', [
            '--class' => $this->Models.'TableSeeder'
        ]);

        $this->info("Scaffolding Complete.");
    }

    private function validateAndSetNames($entity, $arguments)
    {
        $this->Model = str_singular(ucfirst($entity));
        $this->model = str_singular(strtolower($entity));
        $this->Models = str_plural($this->Model);
        $this->models = str_plural(strtolower($this->model));

        $attributes = [];
        foreach($arguments as $k => $argument)
        {
            $arg = explode(':', $argument);
            $field = $arg[0];
            $type = $arg[1] ? $arg[1] : 'string';
            $attributes[$k]['field'] = $field;
            $attributes[$k]['type'] = $type;

            $this->schema .= '$table->'.$type.'("'.$field.'");' . "\n\t\t\t";
            $this->field_names .= "'$field',\n\t\t";
            if($type != 'text')
            {
                $this->table_header .= '<th>'.ucfirst($field).'</th>
            ';
                $this->table_data .= '<td><a href="{{ url("'.$this->models.'", $'.$this->model.'->id) }}">{{ $'.$this->model.'->'.$field.' }}</a></td>
              ';
            }
            $this->details .= '<p><b>'.ucfirst($field).'</b>: {{ $'.$this->model.'->'.$field.' }}</p>
            ';
            $this->seeder_data .= "'$field' => '".ucfirst($field)."',\n\t\t\t";
        }
        return $attributes;
    }

    private function replaceStub($stub)
    {
        $stub = str_replace('{{capSingle}}', $this->Model, $stub);
        $stub = str_replace('{{smallSingle}}', $this->model, $stub);
        $stub = str_replace('{{capPlural}}', $this->Models, $stub);
        $stub = str_replace('{{smallPlural}}', $this->models, $stub);
        return $stub;
    }

    private function replaceFormStub($stub, $attributes)
    {
        $data = '';

        foreach($attributes as $attribute)
        {
            $type = 'text';
            if($attribute['type'] == 'text') $type = 'textarea';
            $data .= '<div class="form-group" >
            {!! Form::label("'.$attribute['field'].'", "'.ucfirst($attribute['field']).':") !!}
            {!! Form::'.$type.'("'.$attribute['field'].'", null, ["class" => "form-control"]) !!}
        </div>
            ';
        }

        return str_replace('{{form_data}}', $data, $stub);
    }

    private function generateSchema($attributes)
    {
        $stub = file_get_contents($this->path . 'SchemaStub.php');
        $stub = str_replace('{{capPlural}}', $this->Models, $stub);
        $stub = str_replace('{{smallPlural}}', $this->models, $stub);
        $stub = str_replace('{{schema}}', $this->schema, $stub);

        file_put_contents('database/migrations/' . date('Y_m_d_His') . '_create_' .$this->models.'_table.php', $stub);
    }

    private function generateController()
    {
        $stub = file_get_contents($this->path . 'ControllerStub.php');
        $stub = $this->replaceStub($stub);
        file_put_contents('app/Http/Controllers/' . $this->Models.'Controller.php', $stub);
    }

    private function generateModel($attributes)
    {
        $stub = file_get_contents($this->path . 'ModelStub.php');
        $stub = str_replace('{{capSingle}}', $this->Model, $stub);
        $stub = str_replace('{{field_names}}', $this->field_names, $stub);
        file_put_contents('app/'.$this->Model.'.php', $stub);
    }

    private function generateViews($attributes)
    {
        if(!is_dir('resources/views/' . $this->models))
            mkdir('resources/views/' . $this->models);

        $stub = file_get_contents($this->path . 'viewsStub/create.blade.php');
        $stub = $this->replaceStub($stub);
        file_put_contents('resources/views/' . $this->models . '/create.blade.php', $stub);

        $stub = file_get_contents($this->path . 'viewsStub/edit.blade.php');
        $stub = $this->replaceStub($stub);
        file_put_contents('resources/views/' . $this->models . '/edit.blade.php', $stub);

        $stub = file_get_contents($this->path . 'viewsStub/form.blade.php');
        $stub = $this->replaceStub($stub);
        $stub = $this->replaceFormStub($stub, $attributes);
        file_put_contents('resources/views/' . $this->models . '/form.blade.php', $stub);

        $stub = file_get_contents($this->path . 'viewsStub/index.blade.php');
        $stub = $this->replaceStub($stub);
        $stub = str_replace('{{table_header}}', $this->table_header, $stub);
        $stub = str_replace('{{table_data}}', $this->table_data, $stub);
        file_put_contents('resources/views/' . $this->models . '/index.blade.php', $stub);

        $stub = file_get_contents($this->path . 'viewsStub/show.blade.php');
        $stub = $this->replaceStub($stub);
        $stub = str_replace('{{details}}', $this->details, $stub);
        file_put_contents('resources/views/' . $this->models . '/show.blade.php', $stub);
    }

    private function seedData($attributes)
    {
        $stub = file_get_contents($this->path . 'SeederStub.php');
        $stub = str_replace('{{capSingle}}', $this->Model, $stub);
        $stub = str_replace('{{capPlural}}', $this->Models, $stub);
        $stub = str_replace('{{seeder_data}}', $this->seeder_data, $stub);
        file_put_contents('database/seeds/'.$this->Models.'TableSeeder.php', $stub);
    }
}
