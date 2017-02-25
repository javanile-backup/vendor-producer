<?php
/**
 * Command line tool for vendor code.
 *
 * PHP version 5
 *
 * @category   CommandLine
 *
 * @author     Francesco Bianco <bianco@javanile.org>
 * @license    https://github.com/Javanile/Producer/blob/master/LICENSE  MIT License
 * @copyright  2015-2017 Javanile.org
 */

namespace Javanile;

/**
 * Class Producer
 * @package Javanile
 */
class Producer
{
    /**
     * Current working directory for running script.
     */
    private $cwd = null;

    /**
     * Entry point for command-line tool.
     */
    public static function cli()
    {
        global $argv;
        $producer = new self();
        echo $producer->run(array_slice($argv, 1));
    }

    /**
     * Script runner.
     */
    private function run($args)
    {
        //
        if (!isset($args[0])) {
            return "> Producer: Command required.\n";
        }

        //
        $this->cwd = getcwd();

        //
        $cmd = $args[0];

        //
        switch ($cmd) {
            case 'init': return $this->cmdInit($args);
            case 'test': return $this->cmdTest($args);
            case 'clone': return $this->cmdClone($args);
            case 'purge': return $this->cmdPurge($args);
            case 'update': return $this->cmdUpdate($args);
            case 'install': return $this->cmdInstall($args);
            case 'publish': return $this->cmdPublish($args);
            default: return "> Producer: undefined '{$cmd}' command.\n";
        }
    }

    /**
     * Init script.
     */
    private function cmdInit($args)
    {
        //
        if (!isset($args[1]) || !$args[1]) {
            return "> Producer: repository url required.\n";
        }

        //
        $repo = trim($args[1]);

        //
        $name = isset($args[2]) ? $args[2] : basename($args[1], '.git');

        //
        $pack = $this->getPackage($args[1]);

        //
        if (!preg_match('/^(http:\/\/|https:\/\/)/i', $repo, $x)) {
            return "> Producer: malformed repository url.\n";
        }

        //
        echo shell_exec(__DIR__.'/../exec/clone-url.sh '.$this->cwd.' '.$repo.' '.$name);

        //
        $comp = $this->cwd.'/repository/'.$name.'/composer.json';

        //
        if (!file_exists($comp)) {

            //
            $json = [
                'name'         => $pack,
                'version'      => '0.0.1',
                'repositories' => [['type' => 'git', 'url' => $repo]],
            ];

            //
            file_put_contents($comp, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }

    /**
     * Test script.
     */
    private function cmdTest($args)
    {
        // test if phpunit are installed
        $phpunit = $this->cwd.'/vendor/bin/phpunit';
        if (!file_exists($phpunit)) {
            return "> Producer: Install phpunit via composer.\n";
        }

        // run all tests on all repository projects
        if (!isset($args[1]) || !$args[1]) {
            $test = 'tests';
            $path = $this->cwd.'/repository';

            foreach (scandir($path) as $name) {
                if ($name[0] != '.' && is_dir($path.'/'.$name)) {
                    echo shell_exec(__DIR__.'/../exec/test-dox.sh '.$this->cwd.' '.$name.' '.$test);
                }
            }

            return;
        }

        // run all tests on one repository project
        $name = $args[1];
        $test = 'tests';
        $path = $this->cwd.'/repository';
        if (is_dir($path.'/'.$name)) {
            return shell_exec(__DIR__.'/../exec/test-dox.sh '.$this->cwd.' '.$name.' '.$test);
        }

        // run single unit test
        $test = 'tests/'.$args[1];
        foreach (scandir($path) as $name) {
            if ($name[0] == '.' || !is_dir($path.'/'.$name)) {
                continue;
            }
            $file = $path.'/'.$name.'/'.$test.'.php';
            if (!file_exists($file)){
                continue;
            }
            $item = isset($args[2]) ? intval($args[2]) : null;
            if (!$item) {
                return shell_exec(__DIR__.'/../exec/test-dox.sh '.$this->cwd.' '.$name.' '.$test);
            }
            require_once $file;
            $class = $args[1];
            if (!class_exists($class)) {
                return "> Producer: Test class '{$class}' not found.";
            }
            $methods = array_filter(get_class_methods($class), function($method){
                return preg_match('/^test[A-Z]/',$method);
            });
            if (!isset($methods[$item-1])) {
                return "> Producer: Test class '{$class}' have less than '{$item}' methods.\n";
            }
            $filter = "'/::".$methods[$item-1]."/'";

            return shell_exec(__DIR__.'/../exec/test-filter.sh '.$this->cwd.' '.$name.' '.$test.' '.$filter);
        }
    }

    /**
     * Clone script.
     */
    private function cmdClone($args)
    {
        //
        if (!isset($args[1]) || !$args[1]) {
            return "> Producer: Repository url or package name required.\n";
        }

        //
        $repo = trim($args[1]);

        //
        if (preg_match('/^(http:\/\/|https:\/\/)/i', $repo, $x)) {

            //
            $name = isset($args[2]) ? $args[2] : basename($args[1], '.git');

            //
            if (is_dir($this->cwd.'/repository/'.$name)) {
                return "> Producer: Project directory 'repository/{$name}' already exists.\n";
            }

            //
            echo shell_exec(__DIR__.'/../exec/clone-url.sh '.$this->cwd.' '.$repo.' '.$name);
            $json = json_decode(file_get_contents($this->cwd.'/repository/'.$name.'/composer.json'));

            return shell_exec(__DIR__.'/../exec/clone-install.sh '.$this->cwd.' '.$json->name.' '.$name);
        }

        //
        elseif (preg_match('/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/', $repo, $x)) {
            echo shell_exec(__DIR__.'/../exec/clone-require.sh '.$this->cwd.' '.$repo);
            $comp = $this->cwd.'/vendor/'.$repo.'/composer.json';
            if (!file_exists($comp)) {
                return "> Producer: Package not found.\n";
            }
            $json = json_decode(file_get_contents($comp));
            $pack = $repo;
            $repo = null;
            if (isset($json->repositories)) {
                foreach ($json->repositories as $item) {
                    if ($item->type == 'git') {
                        $repo = $item->url;
                        break;
                    }
                }
            }
            if ($repo) {
                //
                $name = isset($args[2]) ? $args[2] : basename($repo, '.git');

                //
                if (is_dir($this->cwd.'/repository/'.$name)) {
                    return "> Producer: Project directory 'repository/{$name}' already exists.\n";
                }

                return shell_exec(__DIR__.'/../exec/clone-complete.sh '.$this->cwd.' '.$repo.' '.$name.' '.$pack);
            } else {
                return "> Producer: Repository not found on composer.json.\n";
            }
        } else {
            return "> Producer: Malformed url or package name.\n";
        }
    }

    /**
     * Purge script.
     */
    private function cmdPurge($args)
    {
        //
        if (!isset($args[1]) || !$args[1]) {
            return "> Producer: Project directory required.\n";
        }

        //
        $name = trim($args[1]);

        //
        if (!is_dir($this->cwd.'/repository/'.$name)) {
            return "> Producer: Project directory 'repository/{$name}' not found.\n";
        }

        //
        $json = null;
        $comp = $this->cwd.'/repository/'.$name.'/composer.json';
        if (file_exists($comp)) {
            $json = json_decode(file_get_contents($comp));
        }

        //
        echo shell_exec(__DIR__.'/../exec/purge-rm.sh '.$this->cwd.' '.$name);

        //
        if (isset($json->name)) {
            echo shell_exec(__DIR__.'/../exec/purge-remove.sh '.$this->cwd.' '.$json->name);
        }
    }

    /**
     * Publish script.
     */
    private function cmdUpdate($args)
    {
        //
        if (!isset($args[1]) || !$args[1]) {

            //
            $path = $this->cwd.'/repository';

            //
            foreach (scandir($path) as $name) {
                if ($name[0] != '.' && is_dir($path.'/'.$name)) {
                    echo shell_exec(__DIR__.'/../exec/update.sh '.$this->cwd.' '.$name);
                }
            }
        } else {
            return shell_exec(__DIR__.'/../exec/update.sh '.$this->cwd.' '.$args[1]);
        }
    }

    /**
     * Install script.
     */
    private function cmdInstall($args)
    {
        return "\n";
    }

    /**
     * Publish script.
     */
    private function cmdPublish($args)
    {
        //
        if (!isset($args[1]) || !$args[1]) {

            //
            $path = $this->cwd.'/repository';

            //
            foreach (scandir($path) as $name) {
                if ($name[0] != '.' && is_dir($path.'/'.$name)) {
                    echo shell_exec(__DIR__.'/../exec/publish.sh '.$this->cwd.' '.$name);
                }
            }
        } else {
            return shell_exec(__DIR__.'/../exec/publish.sh '.$this->cwd.' '.$args[1]);
        }
    }

    /**
     * Get package name by repository url.
     */
    private function getPackage($repo)
    {
        //
        $package = basename($repo, '.git');
        $vendor = basename(dirname($repo), '.git');

        //
        return strtolower($vendor.'/'.$package);
    }
}
