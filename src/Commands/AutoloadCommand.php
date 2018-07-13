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

class AutoloadCommand extends Command
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
     * Run autoload command.
     *
     * @param $args
     *
     * @return string
     */
    public function run($args)
    {
        $args = $this->parseArgs($args);

        if (!isset($args[0]) || !$args[0]) {
            return $this->error('&require-package-or-project', ['command' => 'autoload']);
        }

        if ($this->isPackageName($args[0])) {
            if ($this->existsPackageName($args[0])) {
                return $this->exec('autoload', 'autoload', [$args[0]]);
            }

            return $this->autoloadTrikyByPath(
                $this->cwd . '/vendor/' . $args[0],
                'vendor/' . $args[0]
            );
        }

        if (!$this->existsProjectName($args[0])) {
            return $this->error('&project-not-found', ['project' => $args[0]]);
        }

        return $this->autoloadTrikyByPath(
            $this->getProjectDir($args[0]),
            $this->projectsDir . '/' . $args[0]
        );
    }

    protected function autoloadTrikyByPath($path, $diff)
    {
        $from = $path . '/composer.json';
        if (!file_exists($from)) {
            return $this->error('&file-not-found', ['file' => $from, 'command' => 'autoload']);
        }

        $to = $this->cwd . '/composer.json';
        if (!file_exists($to)) {
            return $this->error('&file-not-found', ['file' => $to, 'command' => 'autoload']);
        }

        $fromJson = json_decode(file_get_contents($from), true);
        if (!$fromJson) {
            return $this->error('&json-syntax-error', ['file' => $from, 'command' => 'autoload']);
        }

        $toJson = json_decode(file_get_contents($to), true);
        if (!$toJson) {
            return $this->error('&json-syntax-error', ['file' => $to, 'command' => 'autoload']);
        }

        $fromAutoload = [];
        $this->buildFromAutoload($fromAutoload, $fromJson, 'autoload', $diff);
        $this->buildFromAutoload($fromAutoload, $fromJson, 'autoload-dev', $diff);
        if (!is_array($fromAutoload)) {
            return;
        }

        $toJson = array_replace_recursive($toJson, $fromAutoload);
        if (!$toJson) {
            return;
        }

        file_put_contents($to, json_encode($toJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $this->exec('autoload', 'dump-autoload');
    }

    public function buildFromAutoload(&$fromAutoload, $fromJson, $key, $diff)
    {
        if (isset($fromJson[$key]) && $fromJson[$key]) {
            $fromAutoload[$key] = $fromJson[$key];
            if (isset($fromAutoload[$key]['psr-4'])) {
                foreach ($fromAutoload[$key]['psr-4'] as $namespace => $dir) {
                    $fromAutoload[$key]['psr-4'][$namespace] = $diff . '/' . $dir;
                }
            }
        }
    }
}
