<?php

namespace D3jn\Vizcache\Console\Commands;

class MakeAnalystCommand extends MakeCommand
{
    /**
     * Class type.
     *
     * @var string
     */
    protected $type = 'analyst';

    /**
     * Addition to command's signature.
     *
     * @var string
     */
    protected $additionalSingature = ' {--manager}';

    /**
     * Manager class to link to the analyst.
     *
     * @var string
     */
    protected $hasherClass = 'null';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('manager')) {
            $name = ucwords(camel_case($this->argument('name')));
            $this->managerClass = '\'App\Vizcache\Managers\\' . $name . 'Manager\'';

            parent::handle();

            $this->call('make:vizcache:manager', ['name' => $this->argument('name')]);
        } else {
            parent::handle();
        }
    }

    /**
     * Compile stub template.
     *
     * @param  string $stub
     * @return string
     */
    protected function compileStub(string $stub): string
    {
        $stub = parent::compileStub($stub);

        return str_replace('{{manager}}', $this->hasherClass, $stub);
    }
}
