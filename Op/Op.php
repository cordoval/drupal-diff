<?php

namespace Drupal\Component\Diff\Op;

class Op
{
    protected $type;
    protected $orig;
    protected $closing;

    public function reverse()
    {
        trigger_error('pure virtual', E_USER_ERROR);
    }

    public function norig()
    {
        return $this->orig ? sizeof($this->orig) : 0;
    }

    public function nclosing()
    {
        return $this->closing ? sizeof($this->closing) : 0;
    }
}
