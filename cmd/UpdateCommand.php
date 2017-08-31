<?php
/**
 * Update command for producer.
 *
 * PHP version 5
 *
 * @category   ProducerCommand
 *
 * @author     Francesco Bianco <bianco@javanile.org>
 * @license    https://goo.gl/KPZ2qI  MIT License
 * @copyright  2015-2017 Javanile.org
 */

namespace Javanile\Producer\Commands;

class UpdateCommand extends Command
{
    /**
     * InitCommand constructor.
     *
     * @param $cwd
     */
    public function __construct($cwd)
    {
        parent::__construct($cwd);
    }

    /**
     * Run update command.
     *
     * @param array $args
     *
     * @return string
     */
    public function run($args)
    {
        if (!isset($args[0]) || !$args[0]) {
            return $this->updateEverything();
        }

        $name = $args[0];

        if (!is_dir($this->cwd.'/repository/'.$name)) {
            return $this->error('&required-project');
        }

        echo $this->info("Update project '{$name}'");

        return $this->exec('update', [$name]);
    }

    private function updateEverything()
    {
        // update env
        $env = basename($this->cwd);
        echo "\n> $env\n----------------------------\n";
        echo shell_exec(__DIR__.'/../exec/update-root.sh '.$this->cwd);

        // update all repositories
        $path = $this->cwd.'/repository';
        foreach (scandir($path) as $name) {
            if ($name[0] != '.' && is_dir($path.'/'.$name)) {
                echo "\n> $name\n----------------------------\n";
                echo shell_exec(__DIR__.'/../exec/update.sh '.$this->cwd.' '.$name);
            }
        }
    }
}
