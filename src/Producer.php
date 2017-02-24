<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * PHP version 5
 *
 * @category   CategoryName
 * @package    PackageName
 * @author     Original Author <author@example.com>
 * @author     Another Author <another@example.com>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    SVN: $Id$
 * @link       http://pear.php.net/package/PackageName
 * @see        NetOther, Net_Sample::Net_Sample()
 * @since      File available since Release 1.2.0
 * @deprecated File deprecated in Release 2.0.0
 */

namespace Javanile;

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
            case 'clone': return $this->cmdClone($args);
            case 'purge': return $this->cmdPurge($args);
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
                'repositories' => [['type' => 'git', 'url' => $repo ]],
            ];

            //
            file_put_contents($comp, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
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
        elseif (preg_match('/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/', $repo, $x)){
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
     * Install script.
     */
    private function cmdInstall($args) {
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
     * Get package name by repository url
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
