<?php
/**
 * Clone command for producer.
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

class CloneCommand extends Command
{
    /**
     * CloneCommand constructor.
     *
     * @param $cwd
     */
    public function __construct($cwd)
    {
        parent::__construct($cwd);
    }

    /**
     * Run clone command.
     *
     * @param $args
     *
     * @return string
     */
    public function run($args)
    {
        if (!isset($args[0]) || !$args[0]) {
            return $this->error('&require-package-or-repository');
        }

        if ($this->isUrl($args[0])) {
            return $this->cloneByUrl($args);
        }

        if ($this->isPackageName($args[0])) {
            return $this->cloneByPackageName($args);
        }

        return "> Producer: Malformed url or package name.\n";
    }

    /**
     *
     */
    private function cloneByUrl($args)
    {
        $repo = $args[0];
        $name = isset($args[1]) ? $args[1] : basename($args[0], '.git');

        if (is_dir($this->cwd.'/repository/'.$name)) {
            return "> Producer: Project 'repository/{$name}' already exists.\n";
        }

        echo $this->info("Clone by url '{$repo}'");
        echo $this->exec('clone-url', [$repo, $name]);

        if ($this->hasComposerJson($name)) {
            $pack = $this->getPackageNameByComposerJson($name);
            return $this->exec('clone-install', [$pack, $name]);
        }

        $pack = $this->getPackageNameByUrl($repo);

        return $this->exec('clone-mount', [$pack, $name]);
    }

    /**
     *
     */
    private function cloneByPackageName($args)
    {
        $repo = $args[0];

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
            $name = isset($args[1]) ? $args[1] : basename($repo, '.git');

            //
            if (is_dir($this->cwd.'/repository/'.$name)) {
                return "> Producer: Project directory 'repository/{$name}' already exists.\n";
            }

            return shell_exec(__DIR__.'/../exec/clone-complete.sh '.$this->cwd.' '.$repo.' '.$name.' '.$pack);
        } else {
            return "> Producer: Repository not found on composer.json.\n";
        }
    }
}
