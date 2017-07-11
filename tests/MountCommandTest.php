<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer;
use PHPUnit\Framework\TestCase;
use Javanile\Producer\Commands\MountCommand;

Producer::addPsr4(['Javanile\\Producer\\Tests\\' => __DIR__]);

final class MountCommandTest extends TestCase
{
    use CwdTrait;

    public function testMountGithubProject()
    {
        $cwd = __DIR__.'/cwd';
        $out = shell_exec("cd {$cwd}; composer require javanile/urlman");

        $mount = new MountCommand($cwd);
        $mount->run(['javanile/urlman']);

        $this->assertDirectoryExists(__DIR__.'/cwd/repository/urlman');
        $this->assertDirectoryExists(__DIR__.'/cwd/vendor/javanile/urlman');
    }
}
