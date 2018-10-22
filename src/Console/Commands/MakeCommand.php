<?php

namespace D3jn\Vizcache\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

abstract class MakeCommand extends Command
{
    /**
     * Class name.
     *
     * @var string
     */
    protected $className;

    /**
     * Class type.
     *
     * @var string
     */
    protected $type;

    /**
     * Addition to command's signature.
     *
     * @var string
     */
    protected $additionalSingature = '';

    /**
     * Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files = null;

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->type = strtolower($this->type);

        if (! $this->signature) {
            $this->signature = "make:vizcache:{$this->type} {name}" . $this->additionalSingature;
        }

        if (! $this->description) {
            $this->description = "Create {$this->type} for Vizcache package";
        }

        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->className = $this->getClassName();
        $content = $this->getContent();
        $path = $this->getPath($this->className);

        if ($this->files->exists($path)) {
            return $this->error("Can't create {$this->className} because the file already exists!");
        }

        $dir = dirname($path);
        if (! $this->files->isDirectory($dir)) {
            $this->files->makeDirectory($dir, 0777, true, true);
        }

        $this->files->put($path, $content);
        $this->info("Vizcache {$this->type} created successfully.");
    }

    /**
     * Get path to new class file.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath(string $name): string
    {
        $plural = ucfirst($this->getPluralType());

        return app_path("Vizcache/$plural/$name.php");
    }

    /**
     * Get plural version of type word.
     *
     * @return string
     */
    protected function getPluralType(): string
    {
        return substr($this->type, -1, 1) == 'y'
            ? substr($this->type, 1, -1) . 'ies'
            : $this->type . 's';
    }

    /**
     * Get name for new class from console input.
     *
     * @return string
     */
    protected function getClassName(): string
    {
        $name = $this->argument('name');
        if (! preg_match("~^[A-Za-z_]+" . ucfirst($this->type) . "$~i", $name)) {
            $name .= ucfirst($this->type);
        }

        return ucwords(camel_case($name));
    }

    /**
     * Get content for new class file.
     *
     * @return string
     */
    protected function getContent(): string
    {
        $stub = $this->files->get(__DIR__ . "/../../../stubs/{$this->type}.stub");

        return $this->compileStub($stub);
    }

    /**
     * Compile stub template.
     *
     * @param  string $stub
     * @return string
     */
    protected function compileStub(string $stub): string
    {
        return str_replace('{{class}}', $this->className, $stub);
    }
}
