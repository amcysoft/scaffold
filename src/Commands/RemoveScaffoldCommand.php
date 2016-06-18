<?php

namespace Amcysoft\Scaffold\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\Storage;

class RemoveScaffoldCommand extends Command
{
    private $Model;
    private $model;
    private $Models;
    private $models;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:scaffold {entity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove schema, controller, model, and views';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->Model = '';
        $this->model = '';
        $this->Models = '';
        $this->models = '';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->argument();
        $entity = $this->argument('entity');

        $attributes = $this->validateAndSetNames($entity, $arguments);

        $this->info("Running Auto load ...");
        exec('composer dump-autoload');

        $this->info("Rolling back migration ...");
        \Artisan::call('migrate:rollback');
        
        $this->info("Removing Schema ...");
        $res = glob("database/migrations/*_create_".$this->models."_table.php");
        if(count($res) > 0)
            unlink($res[0]);
        
        $this->info("Removing Controller ...");
        if(file_exists('app/Http/Controllers/' . $this->Models . 'Controller.php'))
            unlink('app/Http/Controllers/' . $this->Models . 'Controller.php');

        $this->info("Removing Model ...");
        if(file_exists('app/' . $this->Model . '.php'))
            unlink('app/' . $this->Model . '.php');

        $this->info("Removing Views ...");
        $p = 'resources/views/' . $this->models;
        if(is_dir($p))
        {
            foreach(scandir($p) as $file)
                if($file != '.' && $file != '..')
                    unlink($p . '/' . $file);
            rmdir($p);
        }

        $this->info("Removing Route ...");
        $r = file_get_contents('app/Http/routes.php');
        $r = str_replace("\nRoute::resource('".$this->models."', '".$this->Models."Controller');", '', $r);
        file_put_contents('app/Http/routes.php', $r);

        $this->info("Removing Seeder ...");
        if(file_exists('database/seeds/' . $this->Models . 'TableSeeder.php'))
            unlink('database/seeds/' . $this->Models . 'TableSeeder.php');

        $this->info("Scaffold removal Complete.");
    }

    private function validateAndSetNames($entity)
    {
        $this->Model = str_singular(ucfirst($entity));
        $this->model = str_singular(strtolower($entity));
        $this->Models = str_plural($this->Model);
        $this->models = str_plural(strtolower($this->model));
    }
}
