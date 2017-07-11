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
        $version = $this->getNextVersion($this->cwd.'/repository/'.$name);

        echo $this->info("Publish project '{$name}' (git login)");

        return $this->exec('publish', [$name, $version]);
    }

    /**
     * Publish everythings.
     */
    private function publishEverything()
    {
        $root = basename($this->cwd);
        $path = $this->cwd.'/repository';
        $version = $this->getNextVersion($this->cwd);

        echo $this->info("Publish root project '{$root}' (git login)");
        echo $this->exec('publish-root', [$version]);

        foreach (scandir($path) as $name) {
            if ($name[0] != '.' && is_dir($path.'/'.$name)) {
                $version = $this->getNextVersion($path.'/'.$name);

                echo "\n";
                echo $this->info("Publish project '{$name}' (git login)");
                echo $this->exec('publish', [$name, $version]);
            }
        }
    }

    /**
     *
     */
    private function getNextVersion($path)
    {
        $file = $path.'/composer.json';

        if (!file_exists($file)) {
            return 'Initial commit';
        }

        $json = json_decode(file_get_contents($file));

        if (!isset($json->version)) {
            return 'Initial commit';
        }

        $ver = explode('.', trim($json->version));
        $min = array_pop($ver);
        $ver[] = $min + 1;
        $json->version = implode('.', $ver);

        $size = file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if (!$size) {
            $this->error("Error to write file '{$file}'.");
        }

        return 'Version '.$json->version;
    }
}
