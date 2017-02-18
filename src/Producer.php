<?php
/**
 *
 *
 */

namespace Javanile;

class Producer {

    /**
     *
     *
     */
    public static function cli() {
        global $argv;
        $producer = new Producer();
        $producer->run(array_slice($argv,1));
    }

    /**
     *
     *
     */
    private function run($args) {

        //
        if (!isset($args[0])) {

        }

        //
        $cmd = $args[0];

        //
        switch ($cmd) {
            case 'publish': return $this->cmdPublish($args);
        }
    }

    /**
     *
     */
    private function cmdPublish($args) {

        //
        $cwd = getcwd();

        //
        $rep = $args[1];

        //
        $out = shell_exec(__DIR__.'/../exec/publish.sh '.$cwd.' '.$rep);

        //
        echo $out;
    }
}
