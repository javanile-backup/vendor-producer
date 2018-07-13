<?php
/**
 * Update command for producer.
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

class UpdateCommand extends Command
{
    /**
     * InitCommand constructor.
     *
     * @param $cwd
     */
    public function __construct($cwd)
    {
        parent::__construct($cwd);
    }

    /**
     * Run update command.
     *
     * @param array $args
     *
     * @return string
     */
    public function run($args)
    {
        $args = $this->parseArgs($args);

        if (!isset($args[0]) || !$args[0]) {
            return $this->updateEverything();
        }

        $projectName = $args[0];
        if (!$this->existsProjectName($projectName)) {
            return $this->error('&required-project');
        }

        echo $this->info("Update project '{$projectName}'");

        return $this->exec('update', 'update-project', [$projectName]);
    }

    /**
     * Update everything inside working directory.
     */
    private function updateEverything()
    {
        // update env
        $env = basename($this->cwd);
        echo "\n> $env\n----------------------------\n";
        $this->exec('update', 'update-root-project');

        // update all projects
        $path = $this->cwd . '/' . $this->projectsDir;
        foreach (scandir($path) as $projectName) {
            if ($projectName[0] != '.' && is_dir($path . '/' . $projectName)) {
                echo "\n> $projectName\n----------------------------\n";
                $this->exec('update', 'update-project', [$projectName]);
            }
        }
    }
}
