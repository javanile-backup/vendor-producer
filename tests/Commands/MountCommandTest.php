<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer\Commands\CloneCommand;
use Javanile\Producer\Commands\MountCommand;
use PHPUnit\Framework\TestCase;

final class MountCommandTest extends TestCase
{
    use CwdTrait;

    public function testMountPackageName()
    {
        $cwd = $this->getCwd();
        $projectDir = $this->getCwd('packages/package-skeleton');

        $this->assertDirectoryNotExists($projectDir);
        $this->assertDirectoryNotExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        $clone = new CloneCommand($this->getCwd());
        $clone->run(['--silent', 'https://github.com/php-code-samples/package-skeleton']);

        $this->assertDirectoryExists($projectDir);
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/package-skeleton'));

        shell_exec("rm -fr {$projectDir}");
        $this->assertDirectoryNotExists($projectDir);

        $mount = new MountCommand($cwd);
        $mount->run(['php-code-samples/package-skeleton', '--silent']);

        $this->assertDirectoryExists($this->getCwd('packages/package-skeleton'));
        $this->assertDirectoryExists($this->getCwd('vendor/php-code-samples/package-skeleton'));
    }
}
