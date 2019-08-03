<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2019-07-23
 */

namespace Larants\Hydrate\Generators\Commands;

use Illuminate\Console\Command;
use Larants\Hydrate\Generators\EntityGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class CreateEntityCommand
 * @package Larants\Hydrate\Generators\Commands
 */
class CreateEntityCommand extends Command
{

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'create:entity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建Entity文件';

    /**
     * @var EntityGenerator
     */
    protected $entityGenerator;


    /**
     * CreateEntityCommand constructor.
     * @param EntityGenerator $entityGenerator
     */
    public function __construct(EntityGenerator $entityGenerator)
    {
        parent::__construct();

        $this->entityGenerator = $entityGenerator;
    }


    /**
     * Execute the console command.
     * @throws \Larants\Hydrate\Exceptions\FileExistsException
     */
    public function handle()
    {
        $options = array_merge(
            $this->arguments(),
            $this->options()
        );
        $this->entityGenerator->setOptions($options);
        $this->entityGenerator->run();
    }


    /**
     * The array of command arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of class being created.', null]
        ];
    }


    /**
     * The array of command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force the creation if file already exists.', null]
        ];
    }


}