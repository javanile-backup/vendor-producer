<?php

namespace Javanile\Producer\Tests;

//use Javanile\Producer;

//Producer::addPsr4(['Javanile\\Producer\\Tests\\' => __DIR__]);

trait CwdTrait
{
    public function setUp()
    {
        $this->gitUser = getenv('PRODUCER_GIT_USER');
        $this->gitPass = getenv('PRODUCER_GIT_PASS');

        $files = __DIR__.'/temp/*';
        shell_exec("rm -fr {$files}");
    }

    public function getCwd($path = '')
    {
        return __DIR__.'/temp/'.$path;
    }
}
