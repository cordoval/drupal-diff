<?php

namespace Drupal\Component\Diff\Op;

use Drupal\Component\Diff\Op;

class Delete extends Op
{
    var $type = 'delete';

    function delete($lines) {
        $this->orig = $lines;
        $this->closing = FALSE;
    }

    function reverse() {
        return new Add($this->orig);
    }
}
