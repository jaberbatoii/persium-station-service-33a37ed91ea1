<?php

namespace Persium\Station\Infrastructures\Persistency\Doctrine\CustomTypes;

class MyDateTime extends \DateTime
{
    public function __toString()
    {
        return $this->format('U');
    }
}
