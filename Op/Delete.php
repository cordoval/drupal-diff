<?php

namespace Drupal\Component\Diff\Op;

class Delete extends Op
{
    protected $type = 'delete';

    public function delete($lines) {
        $this->orig = $lines;
        $this->closing = false;
    }

    public function reverse() {
        return new Add($this->orig);
    }
}
