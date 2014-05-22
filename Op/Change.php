<?php

class _DiffOp_Change extends _DiffOp {
    var $type = 'change';

    function _DiffOp_Change($orig, $closing) {
        $this->orig = $orig;
        $this->closing = $closing;
    }

    function reverse() {
        return new _DiffOp_Change($this->closing, $this->orig);
    }
}