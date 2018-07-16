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
     * @param $repositoryUrl
     *
     * @return bool
     */
    public function existsRepositoryUrl($repositoryUrl)
    {
        $headers = @get_headers($repositoryUrl);
        if (!$headers || $headers[0] == 'HTTP/1.1 404 Not Found') {
            return false;
        }

        return true;
    }

    /**
     * Test is package name.
     *
     * @param mixed $repo
     * @param mixed $packageName
     */
    public function isPackageName($packageName)
    {
        return preg_match('/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/', $packageName);
    }

    /**
     * Test if project has composer.json file.
     *
     * @param mixed $name
     * @param mixed $projectName
     */
    protected function hasComposerJson($projectName)
    {
        return is_dir($this->cwd.'/'.$this->projectsDir.'/'.$projectName)
            && file_exists($this->cwd.'/'.$this->projectsDir.'/'.$projectName.'/composer.json');
    }

    /**
     * Get package name by composer.json file.
     *
     * @param mixed $name
     * @param mixed $projectName
     */
    protected function getPackageNameByComposerJson($projectName)
    {
        $file = $this->cwd.'/'.$this->projectsDir.'/'.$projectName.'/composer.json';
        if (!file_exists($file)) {
            return;
        }

        $json = json_decode(file_get_contents($file));
        if (!isset($json->name) || !$this->isPackageName($json->name)) {
            return;
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
        return file_exists($this->cwd.'/composer.json');
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
                'php' => '*',
            ],
        ];

        file_put_contents($this->cwd.'/composer.json', json_encode($json, JSON_PRETTY_PRINT));
    }

    /**
     * Get package name by repository url.
     *
     * @param mixed $url
     */
    protected function getPackageNameByRepositoryUrl($url)
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
    protected function getProjectNameByRepositoryUrl($url)
    {
        $name = trim(basename($url, '.git'));

        return strtolower($name);
    }

    /**
     * Get project name by repository url.
     *
     * @param mixed $url
     * @param mixed $projectName
     * @param mixed $repositoryUrl
     */
    protected function getProjectPackageName($projectName, $repositoryUrl)
    {
        $packageName = null;

        if ($this->hasComposerJson($projectName)) {
            $packageName = $this->getPackageNameByComposerJson($projectName);
        }

        return $packageName ? $packageName : $this->getPackageNameByRepositoryUrl($repositoryUrl);
    }

    /**
     * Get package name by composer.json file.
     *
     * @param mixed $name
     * @param mixed $packageName
     */
    protected function existsPackageName($packageName)
    {
        if (!$packageName || !$this->isPackageName($packageName)) {
            return false;
        }

        $exists = shell_exec('composer search --only-name '.$packageName);

        return (bool) $exists;
    }

    /**
     * Get package name by composer.json file.
     *
     * @param mixed $name
     */
    protected function getProjectsDir()
    {
        return $this->cwd.'/'.$this->projectsDir;
    }

    /**
     * Get package name by composer.json file.
     *
     * @param mixed $name
     * @param mixed $projectName
     */
    protected function getProjectDir($projectName)
    {
        return $this->cwd.'/'.$this->projectsDir.'/'.$projectName;
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
     * @param mixed $projectName
     */
    protected function existsProjectName($projectName)
    {
        return $this->existsProjectsDir()
            && in_array($projectName, scandir($this->getProjectsDir()));
    }

    /**
     * Return error message.
     *
     * @param mixed      $error
     * @param null|mixed $tokens
     */
    public function error($error, $tokens = null)
    {
        switch ($error) {
            case '&require-package-or-repository':
                $message = 'Repository url or package name required.';
                break;
            case '&require-package-or-project':
                $message = 'Package name or project name required, type \'php producer --help ${command}\'.';
                break;
            case '&help-not-found':
                $message = 'Not found help for \'${command}\' command.';
                break;
            case '&file-not-found':
                $message = 'File not found \'${file}\' during \'${command}\'.';
                break;
            case '&json-syntax-error':
                $message = '[JSON] Syntax error on \'${file}\' during \'${command}\'.';
                break;
            case '&project-not-found':
                $message = 'Project into directory \''.$this->projectsDir.'/${project}\' not found.';
                break;
            default:
                $message = $error;
        }

        if (is_array($tokens) && $tokens) {
            foreach ($tokens as $token => $value) {
                $message = str_replace('${'.$token.'}', $value, $message);
            }
        }

        echo "> Producer: {$message}\n";

        return $error;
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
     * @param mixed $args
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
     *
     * @return string
     */
    protected function escapeParam($param)
    {
        return '"'.$param.'"';
    }

    /**
     * Exec specific script.
     *
     * @param mixed      $exec
     * @param null|mixed $args
     * @param mixed      $cmd
     * @param mixed      $task
     */
    protected function exec($cmd, $task, $args = null)
    {
        $script = __DIR__.'/../../exec/'.$cmd.'/'.$task.'.sh';
        if (!file_exists($script)) {
            die('Producer >  INTERNAL ERROR MISSING SCRIPT');
        }

        $params = '';

        if ($args && count($args) > 0) {
            foreach ($args as &$value) {
                $value = $this->escapeParam($value);
            }

            $params = implode(' ', $args);
        }

        $rawCommand = $script.' '
            .$this->escapeParam($this->cwd).' '
            .$this->escapeParam($this->projectsDir).' '.$params;

        $output = shell_exec($rawCommand);

        if (!$this->silent) {
            echo $output;
        }
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
