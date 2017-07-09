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
     *
     */
    public function __construct($cwd)
    {
        $this->cwd = $cwd;
    }

    /**
     * Test is url.
     */
    public function isUrl($repo)
    {
        return preg_match('/^(http:\/\/|https:\/\/)/i', $repo);
    }

    /**
     * Test is package name.
     */
    public function isPackageName($repo)
    {
        return preg_match('/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/', $repo);
    }

    /**
     * Test if progect has composer.json file.
     */
    protected function hasComposerJson($name)
    {
        return file_exists($this->cwd.'/repository/'.$name.'/composer.json');
    }

    /**
     *
     */
    protected function getPackageNameByComposerJson($name)
    {
        $file = $this->cwd.'/repository/'.$name.'/composer.json';
        $json = json_decode(file_get_contents($file));

        if (!isset($json->name) || !$this->isPackageName($json->name)) {
            $this->error("Package name not found or malformed into '{$file}'.");
            exit(1);
        }

        return $json->name;
    }


    /**
     * Return error message.
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
     *
     */
    protected function info($line)
    {
        return '> Producer: '.$line."\n";
    }

    /**
     * Exec specific script.
     */
    protected function exec($exec, $args = null)
    {
        $script = __DIR__.'/../exec/'.$exec.'.sh';
        $params = '';

        if ($args && count($args) > 0) {
            foreach ($args as &$value) {
                // TODO: fix argument with opportune escapes
            }

            $params = implode(' ', $args);
        }

        return shell_exec($script.' '.$this->cwd.' '.$params);
    }

    /**
     * Override this method.
     */
    public function run($args)
    {
        return "> Producer: You are wellcome!\n";
    }
}
