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
        // init root project
        if (!isset($args[0]) || !$args[0]) {
            $path = $this->cwd;

            return $this->initPath($path);
        }

        // init remote repositiry
        if ($this->isUrl($args[0])) {
            $url = $args[0];
            $clone = new CloneCommand($this->cwd);
            $clone->run([$url]);
            $name = $this->getProjectNameByUrl($url);
            $path = $this->cwd.'/repository/'.$name;

            return $this->initPath($path, $args);
        }

        // init repo project
        if (is_dir($path = $this->cwd.'/repository/'.$args[0])) {
            return $this->initPath($path, $args);
        }

        return "> Producer: malformed init command.\n";
    }

    /**
     * Init script.
     *
     * @param mixed $args
     */
    private function initPath($path, $args = null)
    {
        echo $this->info("Init directory '{$path}'");

        $repo = trim($this->exec('init-origin', [$path]));

        $this->initComposerJson($path, $repo);
        $this->initPackageClassPhp($path, $repo);

        if (!in_array('--no-tests', $args)) {
            $this->initPhpUnitXml($path);
            $this->initPackageClassTestPhp($path, $repo);
        }

        if (!in_array('--no-ci', $args)) {
            //$this->initCodeclimateYml($path, $repo);
            //$this->initTravisYml($path, $repo);
        }
    }

    /**
     * Initialize composer.json file.
     */
    private function initComposerJson($path, $repo)
    {
        $json = [];
        $file = $path.'/composer.json';
        $pack = $this->getPackageNameByUrl($repo);

        if (file_exists($file)) {
            $json = (array) json_decode(file_get_contents($file));
        }

        if (!isset($json['name'])) {
            $json['name'] = $pack;
        }

        if (!isset($json['version'])) {
            $json['version'] = '0.0.1';
        }

        if (!isset($json['repositories'])) {
            $json['repositories'] = [['type' => 'git', 'url' => $repo]];
        }

        $size = file_put_contents(
            $file,
            json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        if (!$size) {
            $this->error("Error to write file '{$file}'.");
        }
    }

    /**
     * Initialize phpunit.xml file.
     */
    private function initPhpUnitXml($path)
    {
        $file = $path.'/phpunit.xml';

        if (!file_exists($file)) {
            copy(__DIR__.'/../tpl/phpunit.xml.txt', $file);
        }
    }

    /**
     * Initialize sample Class.
     *
     * @param mixed $path
     * @param mixed $repo
     */
    private function initPackageClassPhp($path, $repo)
    {
        $class = $this->getClass($repo);
        $namespace = $this->getNamespace($repo);
        $file = $path.'/src/'.$class.'.php';
        if (file_exists($file)) {
            return;
        }
        $code = file_get_contents(__DIR__.'/../tpl/PackageClass.php.txt');
        $code = str_replace(['%%CLASS%%', '%%NAMESPACE%%'], [$class, $namespace], $code);
        if (!is_dir($path.'/src')) {
            mkdir($path.'/src');
        }
        file_put_contents($file, $code);
    }

    /**
     * Initialize sample Test.
     */
    private function initPackageClassTestPhp($path, $repo)
    {
        $class = $this->getClass($repo);
        $namespace = $this->getNamespace($repo);

        if (!file_exists($file = $path.'/tests/'.$class.'Test.php')) {
            $code = str_replace(
                ['%%CLASS%%', '%%NAMESPACE%%'],
                [$class, $namespace],
                file_get_contents(__DIR__.'/../tpl/PackageClassTest.php.txt')
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
    private function initCodeclimateYml($path, $repo)
    {
        $file = $path.'/.codeclimate.yml';
        if (file_exists($file)) {
            return;
        }
        copy(__DIR__.'/../tpl/.codeclimate.yml.txt', $file);
    }

    /**
     * Initialie .travis.yml file.
     *
     * @param mixed $path
     * @param mixed $repo
     */
    private function initTravisYml($path, $repo)
    {
        $file = $path.'/.travis.yml';
        if (file_exists($file)) {
            return;
        }
        copy(__DIR__.'/../tpl/.travis.yml.txt', $file);
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
