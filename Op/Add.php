<?php

namespace Drupal\Component\Diff\Op;

use Drupal\Component\Diff\Op;

class Add extends Op
{
    var $type = 'add';

    function add($lines) {
        $this->closing = $lines;
        $this->orig = FALSE;
    }

    function reverse() {
        return new Delete($this->closing);
    }
}