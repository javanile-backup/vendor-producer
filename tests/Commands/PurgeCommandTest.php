<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer\Commands\CloneCommand;
use Javanile\Producer\Commands\PurgeCommand;
use PHPUnit\Framework\TestCase;

final class PurgeCommandTest extends TestCase
{
    use CwdTrait;

    public function testPurgeProject()
    {
        $clone = new CloneCommand($this->getCwd());

        $this->assertDirectoryNotExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        $clone->run(['--silent', 'https://github.com/php-code-samples/package-skeleton']);

        $this->assertDirectoryExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        $purge = new PurgeCommand($this->getCwd());

        $purge->run(['--silent', 'package-skeleton']);

        $this->assertDirectoryNotExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/package-skeleton'));
    }
}
