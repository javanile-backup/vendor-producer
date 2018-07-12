<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer\Commands\InitCommand;
use Javanile\Producer\Commands\CloneCommand;
use Javanile\Producer\Tests\CwdTrait;
use PHPUnit\Framework\TestCase;

final class InitCommandTest extends TestCase
{
    use CwdTrait;

    public function testInitRootProject()
    {
        $init = new InitCommand($this->getCwd());

        $init->run(['--silent']);

        $this->assertFileExists($this->getCwd('composer.json'));
    }

    public function testInitProject()
    {
        $clone = new CloneCommand($this->getCwd());

        $this->assertDirectoryNotExists($this->getCwd('packages/empty-package'));
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/empty-package'));

        $clone->run(['--silent', 'https://github.com/php-code-samples/empty-package']);

        $this->assertDirectoryExists($this->getCwd('packages/empty-package'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/empty-package'));

        $init = new InitCommand($this->getCwd());

        $init->run(['--silent', 'empty-package']);

        $this->assertFileExists($this->getCwd('packages/empty-package/composer.json'));
        $this->assertFileExists($this->getCwd('vendor/php-code-samples/empty-package/composer.json'));
    }
}
