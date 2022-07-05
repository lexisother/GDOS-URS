<?php

use Extersia\App\App;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModelMakeCommand extends Command
{
    /**
     * The console command name.
     * 
     * @var string
     */
    protected $name = 'make:model';

    /**
     * The console command description.
     * 
     * @var string
     */
    protected $description = 'Creates a new Eloquent model class.';

    /**
     * The type of class being generated.
     * 
     * @var string
     */
    protected $type = 'Model';

    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle()
    {
        // lmao you could take away every function + funky code in this file and
        // uncomment the below code and it would *just work*
        // BUT NO, WE DON'T STOP THERE!!!

        // $name = $this->argument('name');
        // $stubContent = file_get_contents($this->getStub());
        // $stubContent = str_replace("DummyNamespace", "App\Models", $stubContent);
        // $stubContent = str_replace("DummyClass", $name, $stubContent);
        // file_put_contents($path, $this->buildClass($name));

        $name = $this->qualifyClass($this->argument('name'));
        $path = $this->getPath($name);

        if ((!$this->hasOption('force') ||
                !$this->option('force')) &&
            $this->alreadyExists($name)
        ) {
            $this->error($this->type . ' already exists!');

            return false;
        }

        print($path . PHP_EOL);
        print($this->buildClass($name) . PHP_EOL);

        $this->info($this->type . ' created successfully.');
    }

    /**
     * Parse the class name and format according to the root namespace.
     * 
     * @param string $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        $rootNamespace = $this->rootNamespace();

        if (str_starts_with($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name);

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace), '\\') . '\\' . $name
        );
    }

    /**
     * Get the default namespace for the class.
     * 
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Determine if the class already exists.
     * 
     * @param string $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return file_exists($this->getPath($this->qualifyClass($rawName)));
    }

    /**
     * Get the destination class path.
     * 
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace($this->rootNamespace(), '', $name);

        return fullRoot() . '/src/Models' . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Build the class with the given name.
     * 
     * @param string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = file_get_contents($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Replace the namespace for the given stub.
     * 
     * @param string $stub
     * @param string $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel'],
            [$this->getNamespace($name) . '\Models'],
            $stub
        );

        return $this;
    }

    /**
     * Get the full namespace for a given class, without the class name.
     * 
     * @param string $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Replace the class name for the given stub.
     * 
     * @param string $stub
     * @param string $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\\\', '', $name);

        return str_replace('DummyClass', $class, $stub);
    }

    /**
     * Get the stub file for the generator.
     * 
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/model.stub';
    }

    /**
     * Get the root namespace for the class.
     * 
     * @return string
     */
    protected function rootNamespace()
    {
        return App::getNamespace();
    }

    /**
     * Get the console command arguments.
     * 
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
        ];
    }

    /**
     * Get the console command options.
     * 
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists.'],
        ];
    }
}
