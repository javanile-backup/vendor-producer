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

    public function testCloneByRepositoryUrlUnknownPackageName()
    {
        $clone = new CloneCommand($this->getCwd());

        $this->assertDirectoryNotExists($this->getCwd('packages/messy-package'));
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/messy-package'));

        $clone->run([
            '--silent',
            'https://github.com/php-code-samples/messy-package',
        ]);

        $this->assertDirectoryExists($this->getCwd('packages/messy-package'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/messy-package'));
    }

    public function testCloneByPackageName()
    {
        $clone = new CloneCommand($this->getCwd());

        $this->assertDirectoryNotExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        $output = $clone->run([
            'php-code-samples/package-skeleton',
            '--silent',
        ]);

        var_dump($output);

        $this->assertDirectoryExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/package-skeleton'));
    }
}
