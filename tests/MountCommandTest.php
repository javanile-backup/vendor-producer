<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer;
use PHPUnit\Framework\TestCase;

Producer::addPsr4(['Javanile\\Producer\\Tests\\' => __DIR__]);

final class MountCommandTest extends TestCase
{
    use CwdTrait;

    public function testMountGitHubProject()
    {
        $cwd = __DIR__.'/cwd';
        $out = shell_exec("cd {$cwd}; composer require javanile/urlman");

        Producer::log($out);

        //$mount = new CloneCommand();
        //$mount->run(['https://github.com/javanile/urlman']);

        //$this->assertDirectoryExists(__DIR__.'/cwd/repository/urlman');
        //$this->assertDirectoryExists(__DIR__.'/cwd/vendor/javanile/urlman');
    }
}
