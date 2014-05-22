<?php

namespace Drupal\Component\Diff\Op;

class Copy extends Op
{
    protected $type = 'copy';

    public function copy($orig, $closing = FALSE) {
        if (!is_array($closing)) {
            $closing = $orig;
        }
        $this->orig = $orig;
        $this->closing = $closing;
    }

    public function reverse() {
        return new Copy($this->closing, $this->orig);
    }
}