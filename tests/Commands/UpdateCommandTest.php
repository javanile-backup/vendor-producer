<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer\Commands\UpdateCommand;
use Javanile\Producer\Commands\CloneCommand;
use Javanile\Producer\Tests\CwdTrait;
use PHPUnit\Framework\TestCase;

final class UpdateCommandTest extends TestCase
{
    use CwdTrait;

    public function testUpdateEverythings()
    {
        $clone = new CloneCommand($this->getCwd());

        $this->assertDirectoryNotExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        $clone->run(['--silent', 'https://github.com/php-code-samples/package-skeleton']);

        $this->assertDirectoryExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        $update = new UpdateCommand($this->getCwd());

        $uppdate->run(['--silent']);

        $this->assertDirectoryExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/package-skeleton'));
    }

    public function testUpdateProjectName()
    {
        $clone = new CloneCommand($this->getCwd());

        $this->assertDirectoryNotExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        $clone->run(['--silent', 'https://github.com/php-code-samples/package-skeleton']);

        $this->assertDirectoryExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        $update = new UpdateCommand($this->getCwd());

        $uppdate->run(['--silent', 'package-skeleton']);

        $this->assertDirectoryExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/package-skeleton'));
    }
}
