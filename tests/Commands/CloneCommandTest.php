<?php

namespace Javanile\Producer\Tests\Commands;

use Javanile\Producer\Commands\CloneCommand;
use Javanile\Producer\Tests\CwdTrait;
use PHPUnit\Framework\TestCase;

final class CloneCommandTest extends TestCase
{
    use CwdTrait;

    public function testCloneByRepositoryUrl()
    {
        $clone = new CloneCommand($this->getCwd());

        $this->assertDirectoryNotExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        $clone->run([
            '--silent',
            'https://github.com/php-code-samples/package-skeleton',
        ]);

        $this->assertDirectoryExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/package-skeleton'));
    }

    public function testCloneByRepositoryUrlNoMount()
    {
        $clone = new CloneCommand($this->getCwd());

        $this->assertDirectoryNotExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        $clone->run([
            '--silent',
            'https://github.com/php-code-samples/package-skeleton',
            '--no-mount',
        ]);

        $this->assertDirectoryExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/package-skeleton'));
    }

    public function testCloneByPackageName()
    {
        $clone = new CloneCommand($this->getCwd());
        $clone->run(['php-code-samples/package-skeleton']);
        $this->assertDirectoryExists(__DIR__.'/cwd/packages/package-skeleton');
        $this->assertDirectoryExists(__DIR__.'/cwd/vendor/php-code-samples/package-skeleton');
    }

    public function testCloneByRepository()
    {
        $clone = new CloneCommand($this->getCwd());
        $clone->run(['https://github.com/php-code-samples/package-skeleton']);
        $this->assertDirectoryExists(__DIR__.'/cwd/packages/simple-psr-1');
        $this->assertDirectoryExists(__DIR__.'/cwd/vendor/php-source-code/simple-psr-1');
    }
}
