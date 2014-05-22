<?php

namespace Drupal\Component\Diff\Op;

use Drupal\Component\Diff\Op;

class Copy extends Op
{
    var $type = 'copy';

    function copy($orig, $closing = FALSE) {
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