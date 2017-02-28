<?php

namespace Javanile\Producer\Tests;

use PHPUnit\Framework\TestCase;

final class ProducerTest extends TestCase
{
    public function testCloneGitHubProject()
    {
        $cli = new ProducerMock();
        $cli->run(['prova']);
    }
}
