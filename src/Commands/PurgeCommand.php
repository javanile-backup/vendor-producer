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

class PurgeCommand extends Command
{
    /**
     * Purge command constructor.
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
        $args = $this->parseArgs($args);

        if (!isset($args[0]) || !$args[0]) {
            return "> Producer: Project directory required.\n";
        }

        $projectName = trim($args[0]);
        if (!$this->existsProjectName($projectName)) {
            return "> Producer: Project directory '{$this->projectsDir}/{$projectName}' not found.\n";
        }

        $this->info("Purge project '{$projectName}'");

        $json = null;
        $composerJson = $this->cwd.'/'.$this->projectsDir.'/'.$projectName.'/composer.json';
        if (file_exists($composerJson)) {
            $json = json_decode(file_get_contents($composerJson), true);
        }

        if (isset($json['name']) && $this->existsPackageName($json['name'])) {
            $this->exec('purge', 'remove-package', [$json['name']]);
        }

        return $this->exec('purge', 'remove-project', [$projectName]);
    }
}
