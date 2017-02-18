<?php
/**
 *
 *
 */

namespace Javanile;

class Producer
{
    /**
     *
     */
    private $cwd = null;

    /**
     *
     *
     */
    public static function cli()
    {
        global $argv;
        $producer = new Producer();
        $producer->run(array_slice($argv,1));
    }

    /**
     *
     *
     */
    private function run($args)
    {
        //
        $this->cwd = getcwd();

        //
        if (!isset($args[0])) {

        }

        //
        $cmd = $args[0];

        //
        switch ($cmd) {
            case 'clone': return $this->cmdClone($args);
            case 'publish': return $this->cmdPublish($args);
            default: echo "Error undefined command: {$cmd}\n";
        }
    }

    /**
     *
     */
    private function cmdClone($args)
    {
        //
        $rep = $args[1];

        //
        $out = shell_exec(__DIR__.'/../exec/clone.sh '.$this->cwd.' '.$rep);

        //
        echo $out;
    }

    /**
     *
     */
    private function cmdPublish($args)
    {
        //
        $rep = $args[1];

        //
        $out = shell_exec(__DIR__.'/../exec/publish.sh '.$this->cwd.' '.$rep);

        //
        echo $out;
    }
}
