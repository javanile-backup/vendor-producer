<?php
/**
 * Publish command for producer.
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

class PublishCommand extends Command
{
    /**
     * PublishCommand constructor.
     *
     * @param $cwd
     */
    public function __construct($cwd)
    {
        parent::__construct($cwd);
    }

    /**
     * Run publish command.
     *
     * @param $args
     *
     * @return string
     */
    public function run($args)
    {
        if (!isset($args[0]) || !$args[0]) {
            return $this->publishEverything();
        }

        $name = $args[0];
        echo $this->info("Publish project '{$name}' (git login)");
        return $this->exec('publish', [$name]);
    }

    /**
     * Publish everythings.
     */
    private function publishEverything()
    {
        $root = basename($this->cwd);
        $path = $this->cwd.'/repository';

        echo $this->info("Publish root project '{$root}' (git login)");
        echo $this->exec('publish-root');

        foreach (scandir($path) as $name) {
            if ($name[0] != '.' && is_dir($path.'/'.$name)) {
                echo "\n";
                echo $this->info("Publish project '{$name}' (git login)");
                echo $this->exec('publish', [$name]);
            }
        }
    }
}
