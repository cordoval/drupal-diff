<?php

use Drupal\Component\Diff\DiffOp;

class Delete extends DiffOp {
    var $type = 'delete';

    function Delete($lines) {
        $this->orig = $lines;
        $this->closing = FALSE;
    }

    function reverse() {
        return new Add($this->orig);
    }
}
