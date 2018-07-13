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
        $args = $this->parseArgs($args);

        if (!isset($args[0]) || !$args[0]) {
            return $this->publishEverything();
        }

        if (!$this->existsProjectName($args[0])) {
            return $this->error('&project-not-found', ['project' => $args[0]]);
        }

        $projectName = $args[0];
        $version = $this->getNextVersion($this->getProjectDir($projectName));
        $this->info("Publish project '{$projectName}' (git login)");

        return $this->exec('publish', 'publish-project', [$projectName, $version]);
    }

    /**
     * Publish everythings.
     */
    private function publishEverything()
    {
        $root = basename($this->cwd);
        $path = $this->cwd . '/' . $this->projectsDir;
        $version = $this->getNextVersion($this->cwd);

        $this->info("Publish root project '{$root}' (git login)");
        $this->exec('publish', 'publish-root-project', [$version]);

        foreach (scandir($path) as $name) {
            if ($name[0] != '.' && is_dir($path.'/'.$name)) {
                $version = $this->getNextVersion($path.'/'.$name);

                echo "\n";
                $this->info("Publish project '{$name}' (git login)");
                $this->exec('publish', 'publish-project', [$name, $version]);
            }
        }
    }

    /**
     * Get next version number.
     *
     * @param mixed $path
     */
    private function getNextVersion($path)
    {
        $file = $path . '/composer.json';

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
        $size = file_put_contents(
            $file,
            json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        if (!$size) {
            $this->error("Error to write file '{$file}'.");
        }

        return 'Version '.$json->version;
    }
}
