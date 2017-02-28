<?php

use PHPUnit\Framework\TestCase;

final class ProducerTest extends TestCase
{
    public function testCloneGitHubProject()
    {
        $url = Javanile\Urlman\Urlman::current();

        echo $url;

        $this->assertEquals($yaml, ['item' => 'one']);
    }
}
