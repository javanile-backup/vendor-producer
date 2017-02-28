<?php

namespace Javanile\Producer\Tests;

class ProducerMock extends \Javanile\Producer
{
    public function runMock($args)
    {
        $this->run($args);
    }
}


