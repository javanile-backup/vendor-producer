<?php
/**
 * Purge command for producer.
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

class ResetCommand extends Command
{
    /**
     * PurgeCommand constructor.
     *
     * @param $cwd
     */
    public function __construct($cwd)
    {
        parent::__construct($cwd);
    }

    /**
     * Run purge command.
     *
     * @param $args
     *
     * @return string
     */
    public function run($args)
    {
        if (!isset($args[0]) || !$args[0]) {
            return "> Producer: Project directory required.\n";
        }

        $name = trim($args[0]);
        if (!is_dir($path = $this->cwd.'/repository/'.$name)) {
            return "> Producer: Project directory 'repository/{$name}' not found.\n";
        }

        echo $this->info("Reset progect '{$name}'");

        $repo = trim($this->exec('reset-origin', [$path]));

        $purge = new PurgeCommand($this->cwd);
        echo $purge->run([$name]);

        //$clone = new CloneCommand($this->cwd);
        //echo $clone->run([$repo, $name]);
    }
}
