<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer\Commands\InitCommand;
use Javanile\Producer\Commands\CloneCommand;
use Javanile\Producer\Tests\CwdTrait;
use PHPUnit\Framework\TestCase;


final class InitCommandTest extends TestCase
{
    public function testInitRootProject()
    {
        $init = new InitCommand($this->getCwd());

        $init->run(['--silent']);

        $this->assertDirectoryExists($this->getCwd('packages/empty-package'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/empty-package'));
    }

    public function testProjectInit()
    {
        $clone = new CloneCommand($this->getCwd());

        $this->assertDirectoryNotExists($this->getCwd('packages/empty-package'));
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/empty-package'));

        $clone->run([
            '--silent',
            'https://github.com/php-code-samples/empty-package',
        ]);

        $this->assertDirectoryExists($this->getCwd('packages/empty-package'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/empty-package'));

        $init = new InitCommand($this->getCwd());
    }
}
