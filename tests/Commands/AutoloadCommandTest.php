<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer\Commands\AutoloadCommand;
use Javanile\Producer\Commands\CloneCommand;
use PHPUnit\Framework\TestCase;

final class AutoloadCommandTest extends TestCase
{
    use CwdTrait;

    public function testAutoloadPackageName()
    {
        $clone = new CloneCommand($this->getCwd());

        $this->assertDirectoryNotExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        $clone->run(['--silent', 'https://github.com/php-code-samples/package-skeleton']);

        $this->assertDirectoryExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        $autoload = new AutoloadCommand($this->getCwd());

        $autoload->run(['--silent', 'php-code-samples/package-skeleton']);

        $this->assertDirectoryExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/package-skeleton'));
    }
}
