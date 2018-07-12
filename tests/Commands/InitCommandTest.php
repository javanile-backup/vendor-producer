<?php

namespace Javanile\Producer\Tests;

use Javanile\Producer;
use PHPUnit\Framework\TestCase;

final class InitCommandTest extends TestCase
{
    public function testInit()
    {
        // test clone
        $cli = new ProducerMock(__DIR__);
        $cli->runMock(['prova']);
        Producer::log('Hello World!');
        $this->assertEquals(0, 0);
    }
}
