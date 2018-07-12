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

use Stringy\Stringy as S;

class Command
{
    /**
     * Current working directory for running script.
     */
    protected $cwd = null;

    /**
     * Silent mode suppress standard output.
     */
    protected $silent = false;

    /**
     * Projects dir name.
     */
    protected $projectsDir = 'packages';

    /**
     * Command base constructor.
     *
     * @param mixed $cwd
     */
    public function __construct($cwd)
    {
        $this->cwd = $cwd;
    }

    /**
     * Test is url.
     *
     * @param mixed $repo
     */
    public function isUrl($repo)
    {
        return preg_match('/^(http:\/\/|https:\/\/)/i', $repo);
    }

    /**
     * Test is package name.
     *
     * @param mixed $repo
     */
    public function isPackageName($packageName)
    {
        return preg_match('/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/', $packageName);
    }

    /**
     * Test if project has composer.json file.
     *
     * @param mixed $name
     */
    protected function hasComposerJson($project)
    {
        return is_dir($this->cwd.'/packages/'.$project)
            && file_exists($this->cwd.'/packages/'.$project.'/composer.json');
    }

    /**
     * Get package name by composer.json file.
     *
     * @param mixed $name
     */
    protected function getPackageNameByComposerJson($projectName)
    {
        $file = $this->cwd . '/packages/' . $projectName . '/composer.json';
        if (!file_exists($file)) {
            $this->error("Missing file '{$file}'.");
            exit(1);
        }

        $json = json_decode(file_get_contents($file));
        if (!isset($json->name) || !$this->isPackageName($json->name)) {
            $this->error("Package name not found or malformed into '{$file}'.");
            exit(1);
        }

        return $json->name;
    }

    /**
     * Get package name by composer.json file.
     *
     * @param mixed $name
     */
    protected function existsRootComposerJson()
    {
        return file_exists($this->cwd . '/composer.json');
    }

    /**
     * Get package name by composer.json file.
     *
     * @param mixed $name
     */
    protected function createRootComposerJson()
    {
        $json = [
            'require' => [
                'php' => '*'
            ]
        ];

        file_put_contents($this->cwd . '/composer.json', json_encode($json, JSON_PRETTY_PRINT));
    }

    /**
     * Get package name by repository url.
     *
     * @param mixed $url
     */
    protected function getPackageNameByUrl($url)
    {
        $package = trim(basename($url, '.git'));
        $vendor = trim(basename(dirname($url), '.git'));

        return strtolower($vendor.'/'.$package);
    }

    /**
     * Get project name by repository url.
     *
     * @param mixed $url
     */
    protected function getProjectNameByUrl($url)
    {
        $name = trim(basename($url, '.git'));

        return strtolower($name);
    }

    /**
     * Get package name by composer.json file.
     *
     * @param mixed $name
     */
    protected function existsPackageName($packageName)
    {
        if (!$packageName || !$this->isPackageName($packageName)) {
            return false;
        }

        $exists = shell_exec('composer search --only-name ' . $packageName);

        return (boolean) $exists;
    }

    /**
     * Get package name by composer.json file.
     *
     * @param mixed $name
     */
    protected function getProjectsDir()
    {
        return $this->cwd . '/' . $this->projectsDir;
    }

    /**
     * Get package name by composer.json file.
     *
     * @param mixed $name
     */
    protected function existsProjectsDir()
    {
        return is_dir($this->getProjectsDir());
    }

    /**
     * Get package name by composer.json file.
     *
     * @param mixed $name
     */
    protected function existsProjectName($projectName)
    {
        return $this->existsProjectsDir()
            && in_array($projectName, scandir($this->getProgectsDir()));
    }

    /**
     * Return error message.
     *
     * @param mixed $error
     */
    public function error($error)
    {
        switch ($error) {
            case '&require-package-or-repository':
                $message = "> Producer: Repository url or package name required.\n";
                break;
            default:
                $message = $error;
        }

        return $message;
    }

    /**
     * Get an info line.
     *
     * @param mixed $line
     */
    protected function info($line)
    {
        $output = '> Producer: '.$line."\n";

        if (!$this->silent) {
            echo $output;
        }
    }

    /**
     * Get an info line.
     *
     * @param mixed $line
     */
    protected function parseArgs($args)
    {
        if (!is_array($args)) {
            return $args;
        }

        $this->silent = in_array('--silent', $args);
        if ($this->silent) {
            $args = array_values(array_diff($args, ['--silent']));
        }

        return $args;
    }

    /**
     * @param $param
     * @return string
     */
    protected function escapeParam($param)
    {
        return '"' . $param . '"';
    }

    /**
     * Exec specific script.
     *
     * @param mixed      $exec
     * @param null|mixed $args
     */
    protected function exec($cmd, $task, $args = null)
    {
        $script = __DIR__.'/../../exec/'.$cmd.'/'.$task.'.sh';
        $params = '';

        if ($args && count($args) > 0) {
            foreach ($args as &$value) {
                $value = $this->escapeParam($value);
            }

            $params = implode(' ', $args);
        }

        $rawCommand = $script . ' '
            . $this->escapeParam($this->cwd) . ' '
            . $this->escapeParam($this->projectsDir) . ' ' . $params;

        $output = shell_exec($rawCommand);

        if (!$this->silent) {
            echo $output;
        }

        return;
    }

    /**
     * Override this method.
     *
     * @param mixed $args
     */
    public function run($args)
    {
        $args = "'".implode("' '", $args)."'";

        return "> Producer: Sample command with arguments ({$args})\n";
    }
}
