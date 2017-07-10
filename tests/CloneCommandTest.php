<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer;
use PHPUnit\Framework\TestCase;
use Javanile\Producer\Commands\CloneCommand;

Producer::addPsr4(['Javanile\\Producer\\Tests\\' => __DIR__]);

final class CloneCommandTest extends TestCase
{
    use CwdTrait;

    public function testCloneGitHubProject()
    {
        $clone = new CloneCommand(__DIR__.'/cwd');
        $clone->run(['https://github.com/javanile/urlman']);
        $this->assertDirectoryExists(__DIR__.'/cwd/repository/urlman');
    }
}
