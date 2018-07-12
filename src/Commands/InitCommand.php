<?php
/**
 * Init command for producer.
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

class InitCommand extends Command
{
    /**
     * @var array
     */
    protected $options = [
        '--no-ci' => 'noCi',
        '--no-tests' => 'noTests',
    ];

    /**
     * @var boolean
     */
    protected $noCi = false;

    /**
     * @var boolean
     */
    protected $noTests = false;

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
     * Run init command.
     *
     * @param $args
     *
     * @return string
     */
    public function run($args)
    {
        $args = $this->parseArgs($args);
        foreach ($this->options as $argument => $attribute) {
            $this->{$attribute} = in_array($argument, $args);
            if ($this->{$attribute}) {
                $args = array_values(array_diff($args, [$argument]));
            }
        }

        if (!isset($args[0]) || !$args[0]) {
            return $this->initPath($this->cwd);
        }

        $projectName = $args[0];
        if (!$this->existsProjectName($projectName)) {
            return "> Producer: Project '{$this->projectsDir}/{$projectName}' not found.\n";
        }

        return $this->initPath($this->getProjectDir($projectName));
    }

    /**
     * Init script.
     *
     * @param mixed $args
     */
    private function initPath($path, $args = null)
    {
        $this->info("Init directory '{$path}'");

        $repositoryUrl = trim(shell_exec("cd {$path} && git config --get remote.origin.url"));
        $packageName = $this->getPackageNameByRepositoryUrl($repositoryUrl);

        $this->initComposerJson($path, $repositoryUrl, $packageName);
        $this->initPackageClassPhp($path, $packageName);

        if (!$this->noTests) {
            $this->initPhpUnitXml($path);
            $this->initPackageClassTestPhp($path, $packageName);
        }

        if (!$this->noCi) {
            $this->initCodeclimateYml($path);
            $this->initTravisYml($path);
            $this->initStyleCiYml($path);
        }
    }

    /**
     * Initialize composer.json file.
     */
    private function initComposerJson($path, $repositoryUrl, $packageName)
    {
        $json = [];
        $composerJson = $path . '/composer.json';
        if (file_exists($composerJson)) {
            $json = json_decode(file_get_contents($composerJson), true);
        }

        if (!isset($json['name'])) {
            $json['name'] = $packageName;
        }

        if (!isset($json['version'])) {
            $json['version'] = '0.0.1';
        }

        if (!isset($json['repositories'])) {
            $json['repositories'] = [['type' => 'git', 'url' => $repositoryUrl]];
        } else {
            $hasRepositoryUrl = false;
            foreach ($json['repositories'] as $item) {
                if ($item['type'] == 'git') {
                    $hasRepositoryUrl = true;
                    break;
                }
            }
            if (!$hasRepositoryUrl) {
                $json['repositories'][] = ['type' => 'git', 'url' => $repositoryUrl];
            }
        }

        $size = file_put_contents(
            $composerJson,
            json_encode(
                $json,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        if (!$size) {
            $this->error("Error to write file '{$composerJson}'.");
        }
    }

    /**
     * Initialize phpunit.xml file.
     */
    private function initPhpUnitXml($path)
    {
        $file = $path.'/phpunit.xml';

        if (!file_exists($file)) {
            copy(__DIR__.'/../../tpl/phpunit.xml.txt', $file);
        }
    }

    /**
     * Initialize sample Class.
     *
     * @param mixed $path
     * @param mixed $repo
     */
    private function initPackageClassPhp($path, $packageName)
    {
        $class = $this->getClass($packageName);
        $namespace = $this->getNamespace($packageName);
        $file = $path . '/src/' . $class . '.php';
        if (file_exists($file)) {
            return;
        }
        $code = file_get_contents(__DIR__.'/../../tpl/PackageClass.php.txt');
        $code = str_replace(['%%CLASS%%', '%%NAMESPACE%%'], [$class, $namespace], $code);
        if (!is_dir($path.'/src')) {
            mkdir($path.'/src');
        }
        file_put_contents($file, $code);
    }

    /**
     * Initialize sample Test.
     */
    private function initPackageClassTestPhp($path, $packageName)
    {
        $class = $this->getClass($packageName);
        $namespace = $this->getNamespace($packageName);

        if (!file_exists($file = $path . '/tests/' . $class . 'Test.php')) {
            $code = str_replace(
                ['%%CLASS%%', '%%NAMESPACE%%'],
                [$class, $namespace],
                file_get_contents(__DIR__.'/../../tpl/PackageClassTest.php.txt')
            );

            if (!is_dir($path.'/tests')) {
                mkdir($path.'/tests');
            }

            $size = file_put_contents($file, $code);
        }
    }

    /**
     * Initialize .codeclimate.yml file.
     *
     * @param mixed $path
     * @param mixed $repo
     */
    private function initCodeclimateYml($path)
    {
        $file = $path.'/.codeclimate.yml';
        if (file_exists($file)) {
            return;
        }
        copy(__DIR__.'/../../tpl/.codeclimate.yml.txt', $file);
    }

    /**
     * Initialie .travis.yml file.
     *
     * @param mixed $path
     * @param mixed $repo
     */
    private function initTravisYml($path)
    {
        $file = $path.'/.travis.yml';
        if (file_exists($file)) {
            return;
        }
        copy(__DIR__.'/../../tpl/.travis.yml.txt', $file);
    }

    /**
     * Initialie .styleci.yml file.
     *
     * @param mixed $path
     * @param mixed $repo
     */
    private function initStyleCiYml($path)
    {
        $file = $path.'/.styleci.yml';
        if (file_exists($file)) {
            return;
        }
        copy(__DIR__.'/../../tpl/.styleci.yml.txt', $file);
    }

    /**
     * Get package name by repository url.
     *
     * @param mixed $repo
     */
    private function getNamespace($repo)
    {
        $package = trim(ucfirst(S::create(basename($repo, '.git'))->camelize()));
        $vendor = trim(ucfirst(S::create(basename(dirname($repo), '.git'))->camelize()));

        return $vendor.'\\'.$package;
    }

    /**
     * Get class name by repository url.
     *
     * @param mixed $repo
     */
    private function getClass($repo)
    {
        $name = basename($repo, '.git');
        $class = S::create($name)->camelize();

        return ucfirst(trim($class));
    }
}
