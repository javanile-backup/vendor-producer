<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer;
use PHPUnit\Framework\TestCase;
use Javanile\Producer\Commands\CloneCommand;
use Javanile\Producer\Commands\PublishCommand;

Producer::addPsr4(['Javanile\\Producer\\Tests\\' => __DIR__]);

final class PublishCommandTest extends TestCase
{
    use CwdTrait;

    public function testPublishGitHubProject()
    {
        $cwd = __DIR__.'/cwd';
        $clone = new CloneCommand($cwd);
        $clone->run(["https://{$this->gitUser}:{$this->gitPass}@github.com/php-source-code/simple-psr-1"]);

        $publish = new PublishCommand($cwd);
        file_put_contents($cwd.'/repository/simple-psr-1/TIMESTAMP.txt', time());
        $publish->run(['simple-psr-1']);
    }
}
