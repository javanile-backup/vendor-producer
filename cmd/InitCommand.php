<?php
/**
 * Init Command for Producer.
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

class InitCommand extends Command
{
    /**
     * Current working directory for running script.
     */
    private $cwd = null;

    /**
     * InitCommand constructor.
     *
     * @param $cwd
     */
    public function __construct($cwd)
    {
        $this->cwd = $cwd;
    }

    /**
     * Run init command.
     *
     * @param $args
     *
     * @return string
     */
    public function run($args)
    {
        // init env
        if (!isset($args[0]) || !$args[0]) {
            $repo = shell_exec(__DIR__.'/../exec/init-env-origin.sh '.$this->cwd);
            $this->initComposerJson($this->cwd, $repo);
            $this->initPhpUnitXml($this->cwd, $repo);
            echo shell_exec(__DIR__.'/../exec/init-env-update.sh '.$this->cwd);
            return "> Producer: Environment project initialized.\n";
        }

        // init by
        $repo = trim($args[0]);
        if (preg_match('/^(http:\/\/|https:\/\/)/i', $repo)) {
            $name = isset($args[1]) ? $args[1] : basename($args[0], '.git');

            echo shell_exec(__DIR__.'/../exec/clone-url.sh '.$this->cwd.' '.$repo.' '.$name);
        }

        return "> Producer: malformed repository url.\n";
    }

    /**
     *
     */
    private function initComposerJson($path, $repo)
    {
        // init composer.json
        $json = [];
        $file = $path.'/composer.json';
        $pack = $this->getPackage($repo);

        if (file_exists($file)) {
            $json = json_decode(file_get_contents($file));
        }

        if (!isset($json->name)) {
            $json->name = $pack;
        }

        if (!isset($json->version)) {
            $json->version = '0.0.1';
        }

        if (!isset($json->repositories)) {
            $json->repositories = [['type' => 'git', 'url' => $repo]];
        }

        file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     *
     */
    private function initPhpUnitXml($path)
    {
        $file = $path.'/phpunit.xml';
        if (file_exists($file)) {
            return;
        }
        copy(__DIR__.'/../tpl/phpunit.xml.tpl', $file);
    }

    /**
     * Get package name by repository url.
     */
    private function getPackage($repo)
    {
        $package = basename($repo, '.git');
        $vendor = basename(dirname($repo), '.git');

        return strtolower($vendor.'/'.$package);
    }
}
