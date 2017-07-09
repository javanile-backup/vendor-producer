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

class MountCommand extends Command
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
     * Run mount command.
     *
     * @param $args
     *
     * @return string
     */
    public function run($args)
    {
        if (!isset($args[0]) || !$args[0] || !$this->isPackageName($args[0])) {
            return $this->error('&require-package');
        }

        if (!is_dir($this->cwd.'/vendor/'.$args[0])) {
            return $this->error('&package-not-found');
        }

        $name = isset($args[1]) ? $args[1] : basename($args[0]);

        return $this->exec('mount', [$this->cwd, $args[0], $name]);
    }
}
