<?php

namespace Drupal\Component\Diff\Op;

class Add extends Op
{
    protected $type = 'add';

    public function add($lines)
    {
        $this->closing = $lines;
        $this->orig = false;
    }

    public function reverse()
    {
        return new Delete($this->closing);
    }
}