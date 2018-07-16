<?php
/**
 * Mount command for producer.
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

class CheckCommand extends Command
{
    /**
     * MountCommand constructor.
     *
     * @param $cwd
     */
    public function __construct($cwd)
    {
        parent::__construct($cwd);
    }

    /**
     * Run autoload command.
     *
     * @param $args
     *
     * @return string
     */
    public function run($args)
    {
        if (!isset($args[0]) || !$args[0]) {
            return $this->exec('check', [$this->cwd]);
        }

        $name = trim($args[0]);
        if (is_dir($path = $this->cwd.'/repository/'.$name)) {
            return $this->exec('check', [$path]);
        }

        return $this->error('Required project name.');
    }
}
