<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer;

Producer::addPsr4(['Javanile\\Producer\\Tests\\' => __DIR__]);

trait CwdTrait
{
    public function setUp()
    {
        $files = __DIR__.'/cwd/*';
        shell_exec("rm -fr {$files}");
    }
}
