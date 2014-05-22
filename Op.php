<?php

namespace Drupal\Component\Diff;

class Op
{
    var $type;
    var $orig;
    var $closing;

    function reverse() {
        trigger_error('pure virtual', E_USER_ERROR);
    }

    function norig() {
        return $this->orig ? sizeof($this->orig) : 0;
    }

    function nclosing() {
        return $this->closing ? sizeof($this->closing) : 0;
    }
}
