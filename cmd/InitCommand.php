<?php

namespace Javanile\Producer\Commands;

class InitCommand
{
    /**
     * Current working directory for running script.
     */
    private $cwd = null;

    /**
     * InitCommand constructor.
     *
     * @param $cwd
     */
    public function __construct($cwd)
    {
        $this->cwd = $cwd;
    }

    /**
     * Run init command.
     *
     * @param $args
     * @return string
     */
    public function run($args)
    {
        if (!isset($args[0]) || !$args[0]) {
            return "> Producer: repository url required.\n";
        }

        $repo = trim($args[1]);
        $name = isset($args[2]) ? $args[2] : basename($args[1], '.git');
        $pack = $this->getPackage($args[1]);

        if (!preg_match('/^(http:\/\/|https:\/\/)/i', $repo, $x)) {
            return "> Producer: malformed repository url.\n";
        }

        echo shell_exec(__DIR__.'/../exec/clone-url.sh '.$this->cwd.' '.$repo.' '.$name);

        //
        $comp = $this->cwd.'/repository/'.$name.'/composer.json';
        if (!file_exists($comp)) {
            $json = [
                'name'         => $pack,
                'version'      => '0.0.1',
                'repositories' => [['type' => 'git', 'url' => $repo]],
            ];
            file_put_contents($comp, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}
