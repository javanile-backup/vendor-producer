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
        $repo = trim($args[1]);

        //
        $name = isset($args[2]) ? $args[2] : basename($args[1], '.git');

        //
        if (preg_match('/^(http:\/\/|https:\/\/)/i', $repo, $x)) {
            echo shell_exec(__DIR__.'/../exec/clone-url.sh '.$this->cwd.' '.$repo.' '.$name);
            $json = json_decode(file_get_contents($this->cwd.'/repository/'.$name.'/composer.json'));
            echo shell_exec(__DIR__.'/../exec/clone-install.sh '.$this->cwd.' '.$json->name.' '.$name);
        } else {
            echo shell_exec(__DIR__.'/../exec/clone-require.sh '.$this->cwd.' '.$repo.' '.$name);
            $json = json_decode(file_get_contents($this->cwd.'/vendor/'.$repo.'/composer.json'));
            var_Dump($json);
            $pack = $repo;
            $repo = null;
            if (isset($json->repositories)) {
                foreach($json->repositories as $item) {
                    if ($item->type == 'git') {
                        $repo = $item->url;
                        break;
                    }
                }
            }
            if ($repo) {
                echo shell_exec(__DIR__.'/../exec/clone-url.sh '.$this->cwd.' '.$repo.' '.$name);
            } else {
                echo "\n---\nError repository not found on composer.json.";
            }
        }
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
