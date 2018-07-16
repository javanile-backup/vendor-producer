<?php
/**
 * Test command for producer.
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

class TestCommand extends Command
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
     * Run test command.
     *
     * @param $args
     *
     * @return string
     */
    public function run($args)
    {
        // test if phpunit are installed
        $phpunit = $this->cwd.'/vendor/bin/phpunit';
        if (!file_exists($phpunit)) {
            return "> Producer: Install phpunit via composer (not global).\n";
        }

        // run all tests of root project
        if (!isset($args[0]) || !$args[0]) {
            return $this->runRootTests();
        }

        // run all tests of one project
        if (is_dir($this->cwd.'/repository/'.$args[0])) {
            return $this->runProjectTests($args);
        }

        //
        $test = str_replace('\\', '/', $args[0]).'Test';

        // run root-project test file if exist
        if (file_exists($file = $this->cwd.'/tests/'.$test.'.php')) {
            return $this->runFileTests($this->cwd, $test, $file, $args);
        }

        // run single unit test throught repository projects
        $base = $this->cwd.'/repository/';
        foreach (scandir($base) as $name) {
            if ($name[0] == '.' || !is_dir($base.'/'.$name)) {
                continue;
            }
            if (file_exists($file = $base.'/'.$name.'/tests/'.$test.'.php')) {
                return $this->runFileTests($base.'/'.$name, $test, $file, $args);
            }
        }

        return "> Producer: Test case class '{$args[0]}' not found.\n";
    }

    /**
     * Run project tests.
     *
     * @param mixed $args
     */
    private function runRootTests()
    {
        return $this->exec('test', [$this->cwd, 'tests']);
    }

    /**
     * Run project tests.
     *
     * @param mixed $args
     */
    private function runProjectTests($args)
    {
        $name = $args[0];
        $path = $this->cwd.'/repository/'.$name;

        return $this->exec('test', [$path, 'tests']);
    }

    /**
     * Run test inside a test case file.
     *
     * @param mixed $name
     * @param mixed $test
     * @param mixed $file
     * @param mixed $args
     * @param mixed $path
     */
    private function runFileTests($path, $test, $file, $args)
    {
        $item = isset($args[1]) ? intval($args[1]) : null;

        if (!$item) {
            return $this->exec('test', [$path, 'tests/'.$test]);
        }

        $classes = get_declared_classes();
        require_once $file;
        $diff = array_diff(get_declared_classes(), $classes);
        $class = array_pop($diff);

        if (!class_exists($class)) {
            return "> Producer: Test class '{$class}' not found.\n";
        }

        $methods = array_filter(get_class_methods($class), function ($method) {
            return preg_match('/^test[A-Z]/', $method);
        });

        if (!isset($methods[$item - 1])) {
            return "> Producer: Test class '{$class}' have less than '{$item}' methods.\n";
        }

        $filter = '/::'.$methods[$item - 1].'/';

        return $this->exec('test-filter', [$path, 'tests/'.$test, $filter]);
    }
}
