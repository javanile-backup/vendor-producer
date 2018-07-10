<?php

namespace Javanile\Producer\Tests\Commands;

use Javanile\Producer\Commands\CloneCommand;
use Javanile\Producer\Tests\CwdTrait;
use PHPUnit\Framework\TestCase;

//Producer::addPsr4(['Javanile\\Producer\\Tests\\' => __DIR__]);

final class CloneCommandTest extends TestCase
{
    use CwdTrait;

    public function testCloneByRepository()
    {
        $clone = new CloneCommand($this->getCwd());
        $clone->run(['https://github.com/php-code-samples/simple-psr-1']);
        $this->assertDirectoryExists(__DIR__.'/cwd/packages/simple-psr-1');
        $this->assertDirectoryExists(__DIR__.'/cwd/vendor/php-source-code/simple-psr-1');
    }

    public function testCloneByPackageName()
    {
        $clone = new CloneCommand($this->getCwd());
        $clone->run(['php-code-samples/simple-psr-1']);
        $this->assertDirectoryExists(__DIR__.'/cwd/packages/simple-psr-1');
        $this->assertDirectoryExists(__DIR__.'/cwd/vendor/php-source-code/simple-psr-1');
    }
}
