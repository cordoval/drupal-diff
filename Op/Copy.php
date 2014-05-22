<?php

namespace Drupal\Component\Diff\Op;

use Drupal\Component\Diff\DiffOp;

class Copy extends DiffOp
{
    var $type = 'copy';

    function _DiffOp_Copy($orig, $closing = FALSE) {
        if (!is_array($closing)) {
            $closing = $orig;
        }
        $this->orig = $orig;
        $this->closing = $closing;
    }

    function reverse() {
        return new Copy($this->closing, $this->orig);
    }
}