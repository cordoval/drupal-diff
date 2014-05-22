<?php

namespace Drupal\Component\Diff\Op;

class Change extends Op
{
    protected $type = 'change';

    public function change($orig, $closing)
    {
        $this->orig = $orig;
        $this->closing = $closing;
    }

    public function reverse()
    {
        return new Change($this->closing, $this->orig);
    }
}