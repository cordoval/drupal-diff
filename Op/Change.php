<?php

use Drupal\Component\Diff\Op;

class Change extends Op
{
    var $type = 'change';

    function change($orig, $closing) {
        $this->orig = $orig;
        $this->closing = $closing;
    }

    function reverse() {
        return new Change($this->closing, $this->orig);
    }
}