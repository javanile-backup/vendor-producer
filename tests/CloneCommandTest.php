<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer;
use Javanile\Producer\Commands\CloneCommand;
use PHPUnit\Framework\TestCase;

Producer::addPsr4(['Javanile\\Producer\\Tests\\' => __DIR__]);

final class CloneCommandTest extends TestCase
{
    use CwdTrait;

    public function testCloneGitHubProject()
    {
        $clone = new CloneCommand(__DIR__.'/cwd');
        //$clone->run(['https://github.com/javanile/urlman']);
        //$this->assertDirectoryExists(__DIR__.'/cwd/repository/urlman');
        //$this->assertDirectoryExists(__DIR__.'/cwd/vendor/javanile/urlman');
    }

    public function testCloneGitHubEmptyProject()
    {
        $clone = new CloneCommand(__DIR__.'/cwd');
        $clone->run(['https://github.com/php-source-code/simple-psr-1']);
        $this->assertDirectoryExists(__DIR__.'/cwd/repository/simple-psr-1');
        $this->assertDirectoryExists(__DIR__.'/cwd/vendor/php-source-code/simple-psr-1');
    }
}
